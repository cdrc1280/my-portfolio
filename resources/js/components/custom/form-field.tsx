import type { ReactElement, ReactNode } from 'react';
import { cloneElement, isValidElement } from 'react';
import InputError from '@/components/custom/input-error';
import { RequiredMark } from '@/components/custom/required-mark';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';

type FormFieldProps = {
    label: string;
    htmlFor: string;
    error?: string;
    hint?: ReactNode;
    /** Marks the field as required (asterisk + aria-required). Validation itself is done by the server. */
    required?: boolean;
    className?: string;
    children: ReactNode;
};

type ControlProps = {
    'aria-required'?: boolean;
    'aria-invalid'?: boolean;
    'aria-describedby'?: string;
};

/**
 * Label + control + hint or inline error, consistently spaced. Forms set `noValidate`, so errors only
 * ever come from the server and show here, never as the browser's own validation bubbles. A single
 * control child gets aria-required / aria-invalid / aria-describedby wired to the error automatically.
 */
export function FormField({ label, htmlFor, error, hint, required = false, className, children }: FormFieldProps) {
    const errorId = `${htmlFor}-error`;
    const hintId = `${htmlFor}-hint`;
    const describedBy = error ? errorId : hint ? hintId : undefined;

    const control = isValidElement(children)
        ? cloneElement(children as ReactElement<ControlProps>, {
              'aria-required': required || undefined,
              'aria-invalid': error ? true : (children as ReactElement<ControlProps>).props['aria-invalid'],
              'aria-describedby': describedBy,
          })
        : children;

    return (
        <div className={cn('grid content-start gap-2', className)}>
            <Label htmlFor={htmlFor}>
                {label}
                {required && <RequiredMark />}
            </Label>
            {control}
            {hint && !error && (
                <p id={hintId} className="text-xs text-muted-foreground">
                    {hint}
                </p>
            )}
            <InputError id={errorId} message={error} />
        </div>
    );
}
