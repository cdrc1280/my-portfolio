import { Check } from 'lucide-react';
import { cn } from '@/lib/utils';
import type { Tag } from '@/types';

type TagPickerProps = {
    tags: Tag[];
    selected: number[];
    onChange: (ids: number[]) => void;
    labelledBy: string;
};

/** Toggle-button group for choosing a project's technologies. */
export function TagPicker({ tags, selected, onChange, labelledBy }: TagPickerProps) {
    if (tags.length === 0) {
        return <p className="text-sm text-muted-foreground">No tags yet — create some on the Tags page.</p>;
    }

    const toggle = (id: number) =>
        onChange(selected.includes(id) ? selected.filter((value) => value !== id) : [...selected, id]);

    return (
        <div role="group" aria-labelledby={labelledBy} className="flex flex-wrap gap-2">
            {tags.map((tag) => {
                const isSelected = selected.includes(tag.id);

                return (
                    <button
                        key={tag.id}
                        type="button"
                        aria-pressed={isSelected}
                        onClick={() => toggle(tag.id)}
                        className={cn(
                            'inline-flex items-center gap-1 rounded-full border px-3 py-1 text-sm transition-colors focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none',
                            isSelected
                                ? 'border-foreground bg-foreground text-background'
                                : 'text-muted-foreground hover:text-foreground',
                        )}
                    >
                        {isSelected && <Check className="size-3.5" aria-hidden />}
                        {tag.name}
                    </button>
                );
            })}
        </div>
    );
}
