import { Plus, Trash2 } from 'lucide-react';
import { useId } from 'react';
import InputError from '@/components/custom/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { cn } from '@/lib/utils';

type Column<TRow> = {
    key: keyof TRow & string;
    label: string;
    placeholder?: string;
    /** Textarea instead of a single-line input. */
    multiline?: boolean;
};

type RepeatableRowsProps<TRow extends Record<string, string>> = {
    label: string;
    rows: TRow[];
    columns: Array<Column<TRow>>;
    /** A blank row, used by "Add". */
    empty: TRow;
    onChange: (rows: TRow[]) => void;
    /** Validation errors keyed like `metrics.0.label`, plus the list key itself. */
    errors?: Partial<Record<string, string>>;
    errorKey: string;
    max?: number;
    addLabel?: string;
    hint?: string;
};

/**
 * CMS editor for a small list of rows (metrics, engagement types, process steps): add, edit and
 * remove. Empty rows are dropped on the server.
 */
export function RepeatableRows<TRow extends Record<string, string>>({
    label,
    rows,
    columns,
    empty,
    onChange,
    errors = {},
    errorKey,
    max = 8,
    addLabel = 'Add row',
    hint,
}: RepeatableRowsProps<TRow>) {
    const id = useId();
    const update = (index: number, key: keyof TRow, value: string) =>
        onChange(rows.map((row, position) => (position === index ? { ...row, [key]: value } : row)));

    return (
        <fieldset className="grid gap-3">
            <legend className="mb-1 text-sm font-medium">{label}</legend>
            {hint && <p className="-mt-1 text-xs text-muted-foreground">{hint}</p>}
            {rows.map((row, index) => (
                <div key={index} className="flex items-start gap-2 rounded-lg border p-3">
                    <div className={cn('grid flex-1 gap-3', columns.length > 1 && 'sm:grid-cols-2')}>
                        {columns.map((column) => {
                            const fieldId = `${id}-${index}-${column.key}`;
                            const error = errors[`${errorKey}.${index}.${column.key}`];

                            return (
                                <div
                                    key={column.key}
                                    className={cn('grid gap-1.5', column.multiline && 'sm:col-span-2')}
                                >
                                    <Label htmlFor={fieldId} className="text-xs text-muted-foreground">
                                        {column.label}
                                    </Label>
                                    {column.multiline ? (
                                        <Textarea
                                            id={fieldId}
                                            rows={2}
                                            placeholder={column.placeholder}
                                            value={row[column.key]}
                                            onChange={(event) => update(index, column.key, event.target.value)}
                                        />
                                    ) : (
                                        <Input
                                            id={fieldId}
                                            placeholder={column.placeholder}
                                            value={row[column.key]}
                                            onChange={(event) => update(index, column.key, event.target.value)}
                                        />
                                    )}
                                    <InputError message={error} />
                                </div>
                            );
                        })}
                    </div>
                    <Button
                        type="button"
                        variant="ghost"
                        size="icon"
                        className="mt-5"
                        aria-label={`Remove row ${index + 1}`}
                        onClick={() => onChange(rows.filter((_, position) => position !== index))}
                    >
                        <Trash2 aria-hidden />
                    </Button>
                </div>
            ))}
            <div>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    disabled={rows.length >= max}
                    onClick={() => onChange([...rows, { ...empty }])}
                >
                    <Plus aria-hidden /> {addLabel}
                </Button>
            </div>
            <InputError message={errors[errorKey]} />
        </fieldset>
    );
}
