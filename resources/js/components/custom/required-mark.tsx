/**
 * The marker after a required field's label: a red asterisk for sighted users and "(required)" for
 * screen readers. Use it through FormField's `required` prop; use it directly only next to a bare <Label>.
 */
export function RequiredMark() {
    return (
        <>
            <span aria-hidden className="ml-0.5 text-destructive">
                *
            </span>
            <span className="sr-only"> (required)</span>
        </>
    );
}
