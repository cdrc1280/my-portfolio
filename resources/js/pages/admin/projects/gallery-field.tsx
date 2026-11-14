import { X } from 'lucide-react';
import { useId } from 'react';
import { useObjectUrl } from '@/components/custom/image-upload-field';
import InputError from '@/components/custom/input-error';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import type { AdminProject } from '@/types/domains/projects';

type GalleryFieldProps = {
    existing: NonNullable<AdminProject['images']>;
    removedIds: number[];
    newFiles: File[];
    onRemovedIdsChange: (ids: number[]) => void;
    onNewFilesChange: (files: File[]) => void;
    error?: string;
};

/** Manage gallery screenshots: keep, remove existing, add new (with previews). */
export function GalleryField({
    existing,
    removedIds,
    newFiles,
    onRemovedIdsChange,
    onNewFilesChange,
    error,
}: GalleryFieldProps) {
    const id = useId();
    const kept = existing.filter((image) => !removedIds.includes(image.id));

    return (
        <div className="grid grid-cols-1 gap-3">
            <Label htmlFor={id}>Screenshots</Label>
            {(kept.length > 0 || newFiles.length > 0) && (
                <ul className="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    {kept.map((image) => (
                        <Thumbnail
                            key={image.id}
                            src={image.url}
                            label={image.alt ?? 'Screenshot'}
                            onRemove={() => onRemovedIdsChange([...removedIds, image.id])}
                        />
                    ))}
                    {newFiles.map((file, index) => (
                        <NewThumbnail
                            key={`${file.name}-${index}`}
                            file={file}
                            onRemove={() => onNewFilesChange(newFiles.filter((_, i) => i !== index))}
                        />
                    ))}
                </ul>
            )}
            <input
                id={id}
                type="file"
                multiple
                accept="image/jpeg,image/png,image/webp"
                className="w-full max-w-full text-sm file:mr-3 file:rounded-md file:border file:bg-background file:px-3 file:py-1.5 file:text-sm file:font-medium"
                onChange={(event) => {
                    onNewFilesChange([...newFiles, ...Array.from(event.target.files ?? [])]);
                    event.target.value = '';
                }}
            />
            <p className="text-xs text-muted-foreground">Up to 10 images per save. JPG, PNG or WebP, 4 MB each.</p>
            <InputError message={error} />
        </div>
    );
}

function NewThumbnail({ file, onRemove }: { file: File; onRemove: () => void }) {
    const url = useObjectUrl(file);

    return url ? <Thumbnail src={url} label={file.name} onRemove={onRemove} isNew /> : null;
}

function Thumbnail({
    src,
    label,
    onRemove,
    isNew = false,
}: {
    src: string;
    label: string;
    onRemove: () => void;
    isNew?: boolean;
}) {
    return (
        <li className="relative overflow-hidden rounded-lg border">
            <img src={src} alt={label} className="aspect-video w-full object-cover" />
            {isNew && <span className="absolute bottom-1.5 left-1.5 rounded bg-background/90 px-1.5 text-xs">New</span>}
            <Button
                type="button"
                size="icon"
                variant="secondary"
                className="absolute top-1.5 right-1.5 size-7"
                onClick={onRemove}
                aria-label={`Remove ${label}`}
            >
                <X className="size-3.5" aria-hidden />
            </Button>
        </li>
    );
}
