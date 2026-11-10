import { useForm } from '@inertiajs/react';
import { Send } from 'lucide-react';
import type { FormEvent } from 'react';
import { FormField } from '@/components/shared/form-field';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import { store } from '@/routes/contact';

export function ContactForm() {
    const form = useForm({ name: '', email: '', subject: '', message: '', website: '' });
    const { data, setData, errors } = form;

    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.submit(store(), { preserveScroll: true, onSuccess: () => form.reset() });
    };

    return (
        <form onSubmit={submit} className="grid gap-5" noValidate>
            <div className="grid gap-5 sm:grid-cols-2">
                <FormField label="Name" htmlFor="contact-name" error={errors.name}>
                    <Input
                        id="contact-name"
                        autoComplete="name"
                        required
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                    />
                </FormField>
                <FormField label="Email" htmlFor="contact-email" error={errors.email}>
                    <Input
                        id="contact-email"
                        type="email"
                        autoComplete="email"
                        required
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                    />
                </FormField>
            </div>
            <FormField label="Subject" htmlFor="contact-subject" error={errors.subject}>
                <Input id="contact-subject" value={data.subject} onChange={(e) => setData('subject', e.target.value)} />
            </FormField>
            <FormField label="Message" htmlFor="contact-message" error={errors.message}>
                <Textarea
                    id="contact-message"
                    rows={6}
                    required
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
                <Button type="submit" disabled={form.processing}>
                    {form.processing ? <Spinner /> : <Send aria-hidden />}
                    Send message
                </Button>
            </div>
        </form>
    );
}
