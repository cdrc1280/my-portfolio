import { useState } from 'react';
import type { ComponentProps } from 'react';
import { cn } from '@/lib/utils';

type LoadingImageProps = Omit<ComponentProps<'img'>, 'onLoad' | 'onError'> & {
    /** Classes for the wrapper that holds the skeleton (size, aspect ratio, radius, border). */
    frameClassName?: string;
};

/**
 * An <img> that shows a pulsing skeleton until it has loaded, then fades in. Lazy and async by
 * default. If the image fails, the frame stays as a quiet neutral block instead of a broken icon.
 */
export function LoadingImage({
    frameClassName,
    className,
    loading = 'lazy',
    decoding = 'async',
    alt,
    ...props
}: LoadingImageProps) {
    const [state, setState] = useState<'loading' | 'loaded' | 'failed'>('loading');

    return (
        <span
            className={cn(
                'relative block overflow-hidden bg-accent',
                state === 'loading' && 'animate-pulse motion-reduce:animate-none',
                frameClassName,
            )}
        >
            {state !== 'failed' && (
                <img
                    {...props}
                    alt={alt}
                    loading={loading}
                    decoding={decoding}
                    // Cached images may finish before React attaches onLoad.
                    ref={(node) => {
                        if (node?.complete && node.naturalWidth > 0) {
                            setState('loaded');
                        }
                    }}
                    onLoad={() => setState('loaded')}
                    onError={() => setState('failed')}
                    className={cn(
                        'size-full transition-opacity duration-300',
                        state === 'loaded' ? 'opacity-100' : 'opacity-0',
                        className,
                    )}
                />
            )}
        </span>
    );
}
