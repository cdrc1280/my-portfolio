import { useEffect, useState } from 'react';
import DotField from '@/components/custom/dot-field';
import { useReducedMotion } from '@/hooks/use-reduced-motion';

/**
 * Emerald (the `--primary` accent) fading into zinc, as hex because DotField only parses hex and rgb.
 * Keep in sync with the tokens in `resources/css/app.css`.
 */
const PALETTE = {
    light: { from: '#047857', to: '#a1a1aa', glow: '#047857' },
    dark: { from: '#10b981', to: '#52525b', glow: '#10b981' },
} as const;

/** Tracks the `dark` class that `use-appearance` toggles on <html>, including OS theme changes. */
function useIsDark(): boolean {
    const [dark, setDark] = useState(true);

    useEffect(() => {
        const root = document.documentElement;
        const sync = () => setDark(root.classList.contains('dark'));
        sync();

        const observer = new MutationObserver(sync);
        observer.observe(root, { attributes: true, attributeFilter: ['class'] });

        return () => observer.disconnect();
    }, []);

    return dark;
}

/**
 * Decorative backdrop: a faint dot grid that bulges gently around the pointer.
 * Mount it as the first child of a layout root that has `isolate`: it is fixed to the viewport at
 * `-z-10`, so it paints above that root's background but below everything inside it. It ignores
 * pointer events, is hidden from assistive tech, and only renders frames while the pointer moves
 * (or once on resize/theme change), so an idle page costs nothing. With reduced motion it is a
 * static grid that does not react to the pointer.
 */
export function DotFieldBackground() {
    const dark = useIsDark();
    const reduceMotion = useReducedMotion();
    const colors = dark ? PALETTE.dark : PALETTE.light;

    return (
        <div aria-hidden className="pointer-events-none fixed inset-0 -z-10">
            <DotField
                dotRadius={1.25}
                dotSpacing={20}
                gradientFrom={colors.from}
                gradientTo={colors.to}
                glowColor={colors.glow}
                glowIntensity={0.6}
                cursorRadius={240}
                bulgeStrength={0.35}
                baseOpacity={0.6}
                opacity={dark ? 0.5 : 0.42}
                lightMode={!dark}
                interactive={!reduceMotion}
                holdToZoom={false}
                intro={false}
            />
            {/* Fades the grid out under the text column so copy keeps full contrast. */}
            <div className="absolute inset-0 bg-[radial-gradient(ellipse_at_center,var(--background)_15%,transparent_75%)] opacity-45" />
        </div>
    );
}
