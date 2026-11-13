import { useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { FormField } from '@/components/custom/form-field';
import { RepeatableRows } from '@/components/custom/repeatable-rows';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Spinner } from '@/components/ui/spinner';
import { settings } from '@/routes/admin/services';
import type { TitledItem } from '@/types';
import type { EngagementSettings } from '@/types/domains/services';

type EngagementFormData = {
    starting_rate: string;
    engagement_types: TitledItem[];
    process_steps: TitledItem[];
};

const columns = [
    { key: 'title' as const, label: 'Title' },
    { key: 'description' as const, label: 'Description', multiline: true },
];

/** Engagement types, optional "starting at" rate and process steps for the Services page. */
export function EngagementSettingsForm({ engagement }: { engagement: EngagementSettings }) {
    const form = useForm<EngagementFormData>({
        starting_rate: engagement.starting_rate ?? '',
        engagement_types: engagement.engagement_types,
        process_steps: engagement.process_steps,
    });
    const { data, setData, errors } = form;
    const errorBag = errors as Partial<Record<string, string>>;

    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.submit(settings(), { preserveScroll: true });
    };

    return (
        <form onSubmit={submit} noValidate>
            <Card>
                <CardHeader>
                    <CardTitle>Engagement & process</CardTitle>
                    <CardDescription>Shown on the public Services page. Empty rows are ignored.</CardDescription>
                </CardHeader>
                <CardContent className="grid gap-6">
                    <FormField
                        label="Starting rate"
                        htmlFor="starting_rate"
                        error={errors.starting_rate}
                        hint="e.g. $35/hour. Leave empty to hide pricing."
                    >
                        <Input
                            id="starting_rate"
                            value={data.starting_rate}
                            onChange={(e) => setData('starting_rate', e.target.value)}
                        />
                    </FormField>
                    <RepeatableRows
                        label="Engagement types"
                        rows={data.engagement_types}
                        columns={columns}
                        empty={{ title: '', description: '' }}
                        onChange={(rows) => setData('engagement_types', rows)}
                        errors={errorBag}
                        errorKey="engagement_types"
                        max={6}
                        addLabel="Add engagement type"
                    />
                    <RepeatableRows
                        label="Process steps"
                        rows={data.process_steps}
                        columns={columns}
                        empty={{ title: '', description: '' }}
                        onChange={(rows) => setData('process_steps', rows)}
                        errors={errorBag}
                        errorKey="process_steps"
                        addLabel="Add step"
                    />
                    <div>
                        <Button type="submit" disabled={form.processing}>
                            {form.processing && <Spinner />}
                            Save engagement settings
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </form>
    );
}
