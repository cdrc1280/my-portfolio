import { Form, Head } from '@inertiajs/react';
import ProfileController from '@/actions/App/Domains/Account/Controllers/Settings/ProfileController';
import DeleteUser from '@/components/custom/delete-user';
import Heading from '@/components/custom/heading';
import InputError from '@/components/custom/input-error';
import { RequiredMark } from '@/components/custom/required-mark';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useAuthUser } from '@/hooks/use-auth-user';
import { edit } from '@/routes/profile';
export default function Profile() {
    const user = useAuthUser();

    return (
        <>
            <Head title="Profile settings" />

            <h1 className="sr-only">Profile settings</h1>

            <div className="space-y-6">
                <Heading variant="small" title="Profile" description="Update your name and email address" />

                <Form
                    {...ProfileController.update.form()}
                    options={{
                        preserveScroll: true,
                    }}
                    className="space-y-6"
                    noValidate
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="name">
                                    Name
                                    <RequiredMark />
                                </Label>

                                <Input
                                    id="name"
                                    className="mt-1 block w-full"
                                    defaultValue={user.name}
                                    name="name"
                                    required
                                    autoComplete="name"
                                    placeholder="Full name"
                                />

                                <InputError className="mt-2" message={errors.name} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="email">
                                    Email address
                                    <RequiredMark />
                                </Label>

                                <Input
                                    id="email"
                                    type="email"
                                    className="mt-1 block w-full"
                                    defaultValue={user.email}
                                    name="email"
                                    required
                                    autoComplete="username"
                                    placeholder="Email address"
                                />

                                <InputError className="mt-2" message={errors.email} />
                            </div>

                            <div className="flex items-center gap-4">
                                <Button disabled={processing} data-test="update-profile-button">
                                    Save
                                </Button>
                            </div>
                        </>
                    )}
                </Form>
            </div>

            <DeleteUser />
        </>
    );
}

Profile.layout = {
    breadcrumbs: [
        {
            title: 'Profile settings',
            href: edit(),
        },
    ],
};
