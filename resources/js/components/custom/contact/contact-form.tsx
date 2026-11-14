import { useForm } from '@inertiajs/react';
import { Send } from 'lucide-react';
import type { FormEvent } from 'react';
import { FormField } from '@/components/custom/form-field';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import { track } from '@/lib/analytics';
import { succeeded } from '@/lib/form-result';
import { store } from '@/routes/contact';
import type { SelectOption } from '@/types';

type ContactFormData = {
    name: string;
    email: string;
    project_type: string;
    project_type_other: string;
    budget: string;
    budget_currency: string;
    budget_other: string;
    timeline: string;
    timeline_other: string;
    message: string;
    /** Honeypot. */
    website: string;
};

type ContactFormProps = {
    projectTypes: SelectOption[];
    currencies: SelectOption[];
    /** Budget brackets per currency code, labelled in that currency. */
    budgets: Record<string, SelectOption[]>;
    timelines: SelectOption[];
    /** The option value that asks for the sender's own words instead (project type, budget, timeline). */
    otherValue: string;
};

/** Peso for visitors on Philippine time, US dollar otherwise; they can switch either way. */
function defaultCurrency(): string {
    try {
        return Intl.DateTimeFormat().resolvedOptions().timeZone === 'Asia/Manila' ? 'PHP' : 'USD';
    } catch {
        return 'USD';
    }
}

/**
 * Project brief form. Options come from the backend enums (ProjectType, BudgetRange, Currency, ProjectTimeline).
 * The budget brackets are labelled in the chosen currency; the bracket is kept when the currency changes.
 * Picking the "other" option of a dropdown shows a text box under it for the sender's own words.
 * Validation errors show inline under each field; success arrives as a Sonner toast flashed by
 * ContactController, and the sender also gets an auto-reply email.
 */
export function ContactForm({ projectTypes, currencies, budgets, timelines, otherValue }: ContactFormProps) {
    const form = useForm<ContactFormData>({
        name: '',
        email: '',
        project_type: '',
        project_type_other: '',
        budget: '',
        budget_currency: defaultCurrency(),
        budget_other: '',
        timeline: '',
        timeline_other: '',
        message: '',
        website: '',
    });
    const { data, setData, errors } = form;

    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.submit(store(), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: (page) => {
                if (!succeeded(page)) {
                    return;
                }

                track('Contact brief sent', { project_type: data.project_type, currency: data.budget_currency });
                form.reset();
            },
        });
    };

    return (
        <form onSubmit={submit} className="grid gap-5" noValidate>
            <div className="grid gap-5 sm:grid-cols-2">
                <FormField required label="Name" htmlFor="contact-name" error={errors.name}>
                    <Input
                        id="contact-name"
                        autoComplete="name"
                        required
                        aria-invalid={errors.name ? true : undefined}
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                    />
                </FormField>
                <FormField required label="Email" htmlFor="contact-email" error={errors.email}>
                    <Input
                        id="contact-email"
                        type="email"
                        autoComplete="email"
                        required
                        aria-invalid={errors.email ? true : undefined}
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                    />
                </FormField>
            </div>

            <div className="grid gap-5 sm:grid-cols-2">
                <ChoiceField
                    required
                    id="contact-project-type"
                    label="Project type"
                    placeholder="Select a type"
                    options={projectTypes}
                    value={data.project_type}
                    error={errors.project_type}
                    onChange={(value) => setData('project_type', value)}
                    other={{
                        value: otherValue,
                        text: data.project_type_other,
                        placeholder: 'e.g. Mobile app, browser extension',
                        error: errors.project_type_other,
                        onChange: (text) => setData('project_type_other', text),
                    }}
                />
                <ChoiceField
                    required
                    id="contact-timeline"
                    label="Timeline"
                    placeholder="Select a timeline"
                    options={timelines}
                    value={data.timeline}
                    error={errors.timeline}
                    onChange={(value) => setData('timeline', value)}
                    other={{
                        value: otherValue,
                        text: data.timeline_other,
                        placeholder: 'e.g. Before March, in phases',
                        error: errors.timeline_other,
                        onChange: (text) => setData('timeline_other', text),
                    }}
                />
            </div>

            <div className="grid gap-5 sm:grid-cols-[10rem_minmax(0,1fr)]">
                <ChoiceField
                    required
                    id="contact-currency"
                    label="Currency"
                    placeholder="Currency"
                    options={currencies}
                    value={data.budget_currency}
                    error={errors.budget_currency}
                    onChange={(value) => setData('budget_currency', value)}
                />
                <ChoiceField
                    required
                    id="contact-budget"
                    label="Budget"
                    placeholder="Select a range"
                    options={budgets[data.budget_currency] ?? []}
                    value={data.budget}
                    error={errors.budget}
                    onChange={(value) => setData('budget', value)}
                    other={{
                        value: otherValue,
                        text: data.budget_other,
                        placeholder: 'e.g. 20,000 per month',
                        error: errors.budget_other,
                        onChange: (text) => setData('budget_other', text),
                    }}
                />
            </div>

            <FormField
                required
                label="Message"
                htmlFor="contact-message"
                error={errors.message}
                hint="What are you building, what's the timeline, and is there an existing codebase?"
            >
                <Textarea
                    id="contact-message"
                    rows={6}
                    required
                    aria-invalid={errors.message ? true : undefined}
                    value={data.message}
                    onChange={(e) => setData('message', e.target.value)}
                />
            </FormField>

            {/* Honeypot: hidden from people and assistive tech; bots tend to fill it. */}
            <div aria-hidden className="absolute -left-[9999px] h-px w-px overflow-hidden">
                <label htmlFor="contact-website">Website</label>
                <input
                    id="contact-website"
                    name="website"
                    type="text"
                    tabIndex={-1}
                    autoComplete="off"
                    value={data.website}
                    onChange={(e) => setData('website', e.target.value)}
                />
            </div>

            <div>
                <Button type="submit" size="lg" disabled={form.processing}>
                    {form.processing ? <Spinner /> : <Send aria-hidden />}
                    Send message
                </Button>
            </div>
        </form>
    );
}

type OtherAnswer = {
    /** The option value that shows the text box. */
    value: string;
    text: string;
    placeholder: string;
    error?: string;
    onChange: (text: string) => void;
};

type ChoiceFieldProps = {
    id: string;
    label: string;
    placeholder: string;
    options: SelectOption[];
    value: string;
    error?: string;
    required?: boolean;
    onChange: (value: string) => void;
    /** When given, picking `other.value` shows a text box under the dropdown for the sender's own words. */
    other?: OtherAnswer;
};

function ChoiceField({ id, label, placeholder, options, value, error, required, onChange, other }: ChoiceFieldProps) {
    const showOther = other !== undefined && value === other.value;
    const otherId = `${id}-other`;

    return (
        <FormField
            label={label}
            htmlFor={id}
            error={error ?? (showOther ? other.error : undefined)}
            required={required}
        >
            <Select value={value} onValueChange={onChange}>
                <SelectTrigger id={id} className="w-full" aria-invalid={error ? true : undefined}>
                    <SelectValue placeholder={placeholder} />
                </SelectTrigger>
                <SelectContent>
                    {options.map((option) => (
                        <SelectItem key={option.value} value={option.value}>
                            {option.label}
                        </SelectItem>
                    ))}
                </SelectContent>
            </Select>
            {showOther && (
                <Input
                    id={otherId}
                    autoFocus
                    required={required}
                    maxLength={150}
                    aria-label={`${label}: in your own words`}
                    aria-invalid={other.error ? true : undefined}
                    placeholder={other.placeholder}
                    value={other.text}
                    onChange={(e) => other.onChange(e.target.value)}
                />
            )}
        </FormField>
    );
}
