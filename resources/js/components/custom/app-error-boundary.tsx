import { router } from '@inertiajs/react';
import { RotateCcw } from 'lucide-react';
import type { ErrorInfo, ReactNode } from 'react';
import { Component } from 'react';
import { Button } from '@/components/ui/button';

type Props = { children: ReactNode };
type State = { error: Error | null };

/**
 * Last line of defence against a blank screen: if rendering a page throws, show a calm recovery
 * screen (reload or go home) instead of nothing. Resets on the next navigation.
 */
export class AppErrorBoundary extends Component<Props, State> {
    state: State = { error: null };

    private stopListening?: () => void;

    static getDerivedStateFromError(error: Error): State {
        return { error };
    }

    componentDidMount(): void {
        this.stopListening = router.on('navigate', () => {
            if (this.state.error) {
                this.setState({ error: null });
            }
        });
    }

    componentWillUnmount(): void {
        this.stopListening?.();
    }

    componentDidCatch(error: Error, info: ErrorInfo): void {
        console.error('Page crashed while rendering', error, info.componentStack);
    }

    render(): ReactNode {
        if (!this.state.error) {
            return this.props.children;
        }

        return (
            <main
                role="alert"
                className="flex min-h-svh items-center bg-background font-sans text-muted-foreground antialiased"
            >
                <div className="mx-auto w-full max-w-xl space-y-6 px-4 sm:px-6">
                    <p className="font-mono text-xs">
                        <span className="text-primary">{'// '}</span>error
                    </p>
                    <h1 className="text-4xl font-semibold tracking-tight text-foreground">
                        This page couldn&apos;t be shown
                    </h1>
                    <p className="text-lg leading-relaxed">
                        Something went wrong while displaying it. Reloading usually fixes it.
                    </p>
                    <div className="flex flex-wrap gap-3">
                        <Button onClick={() => window.location.reload()}>
                            <RotateCcw aria-hidden /> Reload
                        </Button>
                        <Button variant="outline" asChild>
                            <a href="/">Back to home</a>
                        </Button>
                    </div>
                </div>
            </main>
        );
    }
}
