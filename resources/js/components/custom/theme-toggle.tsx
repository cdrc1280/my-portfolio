import { Moon, Sun } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { useAppearance } from '@/hooks/use-appearance';

/** Flips between dark (the default) and light; the choice persists in localStorage and a cookie. */
export function ThemeToggle() {
    const { resolvedAppearance, updateAppearance } = useAppearance();
    const next = resolvedAppearance === 'dark' ? 'light' : 'dark';

    return (
        <Button
            variant="ghost"
            size="icon"
            onClick={() => updateAppearance(next)}
            aria-label={`Switch to ${next} mode`}
            className="text-muted-foreground hover:text-foreground"
        >
            <Sun className="hidden size-[18px] dark:block" aria-hidden />
            <Moon className="size-[18px] dark:hidden" aria-hidden />
        </Button>
    );
}
