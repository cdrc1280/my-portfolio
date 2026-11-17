import { Form } from '@inertiajs/react';
import { useRef } from 'react';
import ProfileController from '@/actions/App/Domains/Account/Controllers/Settings/ProfileController';
import Heading from '@/components/custom/heading';
import InputError from '@/components/custom/input-error';
import PasswordInput from '@/components/custom/password-input';
import { RequiredMark } from '@/components/custom/required-mark';
import {
    ResponsiveDialog,
    ResponsiveDialogBody,
    ResponsiveDialogClose,
    ResponsiveDialogContent,
    ResponsiveDialogDescription,
    ResponsiveDialogFooter,
    ResponsiveDialogHeader,
    ResponsiveDialogTitle,
    ResponsiveDialogTrigger,
    useResponsiveDialogFormClass,
} from '@/components/custom/responsive-dialog';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';

export default function DeleteUser() {
    return (
        <div className="space-y-6">
            <Heading
                variant="small"
                title="Delete account"
                description="Delete your account and all of its resources"
            />
            <div className="space-y-4 rounded-lg border border-red-100 bg-red-50 p-4 dark:border-red-200/10 dark:bg-red-700/10">
                <div className="relative space-y-0.5 text-red-600 dark:text-red-100">
                    <p className="font-medium">Warning</p>
                    <p className="text-sm">Please proceed with caution, this cannot be undone.</p>
                </div>

                <ResponsiveDialog>
                    <ResponsiveDialogTrigger>
                        <Button variant="destructive" data-test="delete-user-button">
                            Delete account
                        </Button>
                    </ResponsiveDialogTrigger>
                    <ResponsiveDialogContent>
                        <DeleteUserForm />
                    </ResponsiveDialogContent>
                </ResponsiveDialog>
            </div>
        </div>
    );
}

/** Inside the dialog content so the form layout follows the dialog/drawer switch. */
function DeleteUserForm() {
    const passwordInput = useRef<HTMLInputElement>(null);
    const formClass = useResponsiveDialogFormClass();

    return (
        <Form
            noValidate
            {...ProfileController.destroy.form()}
            options={{
                preserveScroll: true,
            }}
            onError={() => passwordInput.current?.focus()}
            resetOnSuccess
            className={formClass}
        >
            {({ resetAndClearErrors, processing, errors }) => (
                <>
                    <ResponsiveDialogBody>
                        <ResponsiveDialogHeader>
                            <ResponsiveDialogTitle>Are you sure you want to delete your account?</ResponsiveDialogTitle>
                            <ResponsiveDialogDescription>
                                Once your account is deleted, all of its resources and data will also be permanently
                                deleted. Please enter your password to confirm you would like to permanently delete your
                                account.
                            </ResponsiveDialogDescription>
                        </ResponsiveDialogHeader>
                        <div className="grid gap-2">
                            <Label htmlFor="password" className="sr-only">
                                Password
                                <RequiredMark />
                            </Label>

                            <PasswordInput
                                id="password"
                                required
                                name="password"
                                ref={passwordInput}
                                placeholder="Password"
                                autoComplete="current-password"
                            />

                            <InputError message={errors.password} />
                        </div>
                    </ResponsiveDialogBody>

                    <ResponsiveDialogFooter className="gap-2">
                        <ResponsiveDialogClose>
                            <Button variant="secondary" onClick={() => resetAndClearErrors()}>
                                Cancel
                            </Button>
                        </ResponsiveDialogClose>

                        <Button variant="destructive" disabled={processing} asChild>
                            <button type="submit" data-test="confirm-delete-user-button">
                                Delete account
                            </button>
                        </Button>
                    </ResponsiveDialogFooter>
                </>
            )}
        </Form>
    );
}
