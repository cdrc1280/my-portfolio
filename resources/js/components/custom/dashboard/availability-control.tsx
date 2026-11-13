import { useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import InputError from '@/components/custom/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { ToggleGroup, ToggleGroupItem } from '@/components/ui/toggle-group';
import { cn } from '@/lib/utils';
import { update } from '@/routes/admin/availability';
import type { AvailabilityStatus } from '@/types';
import type { AvailabilityControlProps } from '@/types/domains/dashboard';

const dot: Record<AvailabilityStatus, string> = {
    available: 'bg-primary',
    limited: 'bg-amber-500',
    unavailable: 'bg-muted-foreground',
};

/** Switch the public availability badge without leaving the dashboard. */
export function AvailabilityControl({ status, note, options }: AvailabilityControlProps) {
    const form = useForm({ availability_status: status, availability: note ?? '' });
    const { data, setData, errors } = form;
    const dirty = data.availability_status !== status || data.availability !== (note ?? '');

    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.submit(update(), { preserveScroll: true });
    };

    return (
        <form onSubmit={submit} className="space-y-4" noValidate>
            <ToggleGroup
                type="single"
                variant="outline"
                value={data.availability_status}
                onValueChange={(value) => value && setData('availability_status', value as AvailabilityStatus)}
                className="grid w-full grid-cols-3"
                aria-label="Availability"
            >
                {options.map((option) => (
                    <ToggleGroupItem key={option.value} value={option.value} className="gap-1.5 text-xs">
                        <span className={cn('size-2 rounded-full', dot[option.value])} aria-hidden />
                        {option.value}
                    </ToggleGroupItem>
                ))}
            </ToggleGroup>
            <InputError message={errors.availability_status} />
            <div className="grid gap-1.5">
                <Label htmlFor="availability-note" className="text-xs text-muted-foreground">
                    Note shown next to the badge
                </Label>
                <Input
                    id="availability-note"
                    value={data.availability}
                    placeholder="e.g. Open to part-time contracts"
                    onChange={(event) => setData('availability', event.target.value)}
                />
                <InputError message={errors.availability} />
            </div>
            <Button type="submit" size="sm" disabled={!dirty || form.processing}>
                {form.processing && <Spinner />}
                Update badge
            </Button>
        </form>
    );
}
