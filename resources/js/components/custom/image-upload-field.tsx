import { ImageIcon, X } from 'lucide-react';
import { useEffect, useId, useMemo } from 'react';
import InputError from '@/components/custom/input-error';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';

type ImageUploadFieldProps = {
    label: string;
    /** URL of the image currently saved on the server, if any. */
    currentUrl: string | null;
    file: File | null;
    /** True when the user asked to remove the saved image. */
    removed: boolean;
    onFileChange: (file: File | null) => void;
    onRemovedChange: (removed: boolean) => void;
    error?: string;
    hint?: string;
};

/** Single image picker with live preview and a remove toggle. */
export function ImageUploadField({
    label,
    currentUrl,
    file,
    removed,
    onFileChange,
    onRemovedChange,
    error,
    hint = 'JPG, PNG or WebP, up to 4 MB.',
}: ImageUploadFieldProps) {
    const id = useId();
    const previewUrl = useObjectUrl(file);
    const shownUrl = previewUrl ?? (removed ? null : currentUrl);

    return (
        <div className="grid gap-2">
            <Label htmlFor={id}>{label}</Label>
            <div className="flex items-start gap-4">
                <div className="flex aspect-video w-48 shrink-0 items-center justify-center overflow-hidden rounded-lg border bg-muted">
                    {shownUrl ? (
                        <img src={shownUrl} alt={`${label} preview`} className="size-full object-cover" />
                    ) : (
                        <ImageIcon className="size-6 text-muted-foreground" aria-hidden />
                    )}
                </div>
                <div className="grid gap-2">
                    <input
                        id={id}
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                        className="text-sm file:mr-3 file:rounded-md file:border file:bg-background file:px-3 file:py-1.5 file:text-sm file:font-medium"
                        onChange={(event) => {
                            onFileChange(event.target.files?.[0] ?? null);
                            onRemovedChange(false);
                        }}
                    />
                    <p className="text-xs text-muted-foreground">{hint}</p>
                    {(file || (currentUrl && !removed)) && (
                        <Button
                            type="button"
                            variant="ghost"
                            size="sm"
                            className="w-fit"
                            onClick={() => {
                                onFileChange(null);
                                onRemovedChange(currentUrl !== null);
                            }}
                        >
                            <X aria-hidden /> Remove image
                        </Button>
                    )}
                </div>
            </div>
            <InputError message={error} />
        </div>
    );
}

/** Creates (and revokes) a blob URL for a locally selected file. */
export function useObjectUrl(file: File | null): string | null {
    const url = useMemo(() => (file ? URL.createObjectURL(file) : null), [file]);

    useEffect(() => {
        return () => {
            if (url) {
                URL.revokeObjectURL(url);
            }
        };
    }, [url]);

    return url;
}
