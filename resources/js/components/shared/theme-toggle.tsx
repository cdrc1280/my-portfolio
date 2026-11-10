import { Moon, Sun } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { useAppearance } from '@/hooks/use-appearance';

/** Flips between light and dark; the choice is remembered in a cookie. */
export function ThemeToggle() {
    const { resolvedAppearance, updateAppearance } = useAppearance();
    const next = resolvedAppearance === 'dark' ? 'light' : 'dark';

    return (
        <Button
            variant="ghost"
            size="icon"
            onClick={() => updateAppearance(next)}
            aria-label={`Switch to ${next} mode`}
        >
            <Sun className="hidden size-4 dark:block" aria-hidden />
            <Moon className="size-4 dark:hidden" aria-hidden />
        </Button>
    );
}
