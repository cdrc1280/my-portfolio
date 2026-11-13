import { ContentIcon } from '@/components/custom/content-icon';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { contentIconNames } from '@/lib/icons';

const NONE = '__none';

/** CMS picker for a ContentIcon name. Empty string means "no icon". */
export function IconSelect({ id, value, onChange }: { id: string; value: string; onChange: (value: string) => void }) {
    return (
        <Select value={value === '' ? NONE : value} onValueChange={(next) => onChange(next === NONE ? '' : next)}>
            <SelectTrigger id={id} className="w-full">
                <SelectValue />
            </SelectTrigger>
            <SelectContent>
                <SelectItem value={NONE}>No icon</SelectItem>
                {contentIconNames.map((name) => (
                    <SelectItem key={name} value={name}>
                        <ContentIcon icon={name} className="size-4" />
                        {name}
                    </SelectItem>
                ))}
            </SelectContent>
        </Select>
    );
}
