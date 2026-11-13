import { Link } from '@inertiajs/react';
import { Quote } from 'lucide-react';
import { LoadingImage } from '@/components/custom/loading-image';
import { show } from '@/routes/projects';
import type { Testimonial } from '@/types/domains/testimonials';

/** A client quote with attribution and, when linked, the related case study. */
export function TestimonialCard({
    testimonial,
    showProject = true,
}: {
    testimonial: Testimonial;
    showProject?: boolean;
}) {
    const attribution = [testimonial.author_role, testimonial.company].filter(Boolean).join(', ');

    return (
        <figure className="flex h-full flex-col gap-5 rounded-lg border border-border bg-card p-6">
            <Quote className="size-5 text-primary" aria-hidden />
            <blockquote className="flex-1 leading-relaxed text-foreground">“{testimonial.quote}”</blockquote>
            <figcaption className="flex items-center gap-3">
                {testimonial.photo_url && (
                    <LoadingImage
                        src={testimonial.photo_url}
                        alt=""
                        width={40}
                        height={40}
                        frameClassName="size-10 shrink-0 rounded-full border border-border"
                        className="object-cover"
                    />
                )}
                <div className="min-w-0 text-sm">
                    <p className="font-medium text-foreground">{testimonial.author_name}</p>
                    {attribution && <p className="truncate">{attribution}</p>}
                    {showProject && testimonial.project && (
                        <Link
                            href={show(testimonial.project.slug)}
                            className="font-mono text-xs text-primary underline-offset-4 hover:underline"
                        >
                            {testimonial.project.title}
                        </Link>
                    )}
                </div>
            </figcaption>
        </figure>
    );
}
