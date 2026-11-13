import { LoadingImage } from '@/components/custom/loading-image';
import type { ProjectImage } from '@/types/domains/projects';

/** Screenshots: bordered, rounded, lazy-loaded; each opens full size in a new tab. */
export function ProjectGallery({ images }: { images: ProjectImage[] }) {
    if (images.length === 0) {
        return null;
    }

    return (
        <ul className="grid gap-4 sm:grid-cols-2">
            {images.map((image) => (
                <li key={image.id}>
                    <a
                        href={image.url}
                        target="_blank"
                        rel="noreferrer"
                        className="block overflow-hidden rounded-lg border border-border transition-colors duration-150 hover:border-border-strong"
                    >
                        <LoadingImage
                            src={image.url}
                            alt={image.alt}
                            width={1200}
                            height={675}
                            frameClassName="aspect-video w-full"
                            className="object-cover"
                        />
                        <span className="sr-only"> (opens full size in a new tab)</span>
                    </a>
                </li>
            ))}
        </ul>
    );
}
