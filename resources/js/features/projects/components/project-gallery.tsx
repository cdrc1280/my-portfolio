import type { ProjectImage } from '@/features/projects/types';

export function ProjectGallery({ images }: { images: ProjectImage[] }) {
    if (images.length === 0) {
        return null;
    }

    return (
        <section aria-labelledby="gallery-heading" className="space-y-4">
            <h2 id="gallery-heading" className="text-lg font-semibold tracking-tight">
                Screenshots
            </h2>
            <ul className="grid gap-4 sm:grid-cols-2">
                {images.map((image) => (
                    <li key={image.id}>
                        <a
                            href={image.url}
                            target="_blank"
                            rel="noreferrer"
                            className="block overflow-hidden rounded-lg border"
                        >
                            <img
                                src={image.url}
                                alt={image.alt}
                                loading="lazy"
                                className="aspect-video w-full object-cover"
                            />
                        </a>
                    </li>
                ))}
            </ul>
        </section>
    );
}
