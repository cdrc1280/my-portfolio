import type { ReactNode } from 'react';
import { FormDialog } from '@/components/custom/form-dialog';
import { FormField } from '@/components/custom/form-field';
import { ImageUploadField } from '@/components/custom/image-upload-field';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { useDialogForm } from '@/hooks/use-dialog-form';
import { store, update } from '@/routes/admin/testimonials';
import type { SelectOption } from '@/types';
import type { AdminTestimonial } from '@/types/domains/testimonials';

const NO_PROJECT = '__none';

type TestimonialFormData = {
    author_name: string;
    author_role: string;
    company: string;
    quote: string;
    project_id: string;
    has_permission: boolean;
    is_featured: boolean;
    photo: File | null;
    remove_photo: boolean;
};

type TestimonialDialogProps = {
    testimonial?: AdminTestimonial;
    projects: SelectOption[];
    trigger: ReactNode;
};

export function TestimonialDialog({ testimonial, projects, trigger }: TestimonialDialogProps) {
    const { open, setOpen, form, submit } = useDialogForm<TestimonialFormData>(
        {
            author_name: testimonial?.author_name ?? '',
            author_role: testimonial?.author_role ?? '',
            company: testimonial?.company ?? '',
            quote: testimonial?.quote ?? '',
            project_id: testimonial?.project_id ? String(testimonial.project_id) : '',
            has_permission: testimonial?.has_permission ?? false,
            is_featured: testimonial?.is_featured ?? false,
            photo: null,
            remove_photo: false,
        },
        {
            storeUrl: store.url(),
            updateUrl: testimonial ? update.url(testimonial.id) : undefined,
            multipart: true,
        },
    );
    const { data, setData, errors } = form;

    return (
        <FormDialog
            open={open}
            onOpenChange={setOpen}
            trigger={trigger}
            title={testimonial ? 'Edit testimonial' : 'Add testimonial'}
            description="Only testimonials with the client’s permission are shown on the site."
            processing={form.processing}
            onSubmit={submit}
            size="lg"
        >
            <div className="grid gap-4 sm:grid-cols-3">
                <FormField required label="Author" htmlFor="t-author" error={errors.author_name}>
                    <Input
                        id="t-author"
                        value={data.author_name}
                        onChange={(e) => setData('author_name', e.target.value)}
                    />
                </FormField>
                <FormField label="Role" htmlFor="t-role" error={errors.author_role}>
                    <Input
                        id="t-role"
                        value={data.author_role}
                        onChange={(e) => setData('author_role', e.target.value)}
                    />
                </FormField>
                <FormField label="Company" htmlFor="t-company" error={errors.company}>
                    <Input id="t-company" value={data.company} onChange={(e) => setData('company', e.target.value)} />
                </FormField>
            </div>
            <FormField required label="Quote" htmlFor="t-quote" error={errors.quote}>
                <Textarea id="t-quote" rows={4} value={data.quote} onChange={(e) => setData('quote', e.target.value)} />
            </FormField>
            <FormField
                label="Related project"
                htmlFor="t-project"
                error={errors.project_id}
                hint="Shown on that case study."
            >
                <Select
                    value={data.project_id === '' ? NO_PROJECT : data.project_id}
                    onValueChange={(value) => setData('project_id', value === NO_PROJECT ? '' : value)}
                >
                    <SelectTrigger id="t-project" className="w-full">
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value={NO_PROJECT}>None</SelectItem>
                        {projects.map((project) => (
                            <SelectItem key={project.value} value={project.value}>
                                {project.label}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
            </FormField>
            <ImageUploadField
                label="Photo"
                currentUrl={testimonial?.photo_url ?? null}
                file={data.photo}
                removed={data.remove_photo}
                onFileChange={(file) => setData('photo', file)}
                onRemovedChange={(removed) => setData('remove_photo', removed)}
                error={errors.photo}
                hint="Square, up to 1 MB."
            />
            <div className="grid gap-3">
                <div className="flex items-center gap-2">
                    <Checkbox
                        id="t-permission"
                        checked={data.has_permission}
                        onCheckedChange={(checked) => setData('has_permission', checked === true)}
                    />
                    <Label htmlFor="t-permission" className="font-normal">
                        The client agreed to have this published
                    </Label>
                </div>
                <div className="flex items-center gap-2">
                    <Checkbox
                        id="t-featured"
                        checked={data.is_featured}
                        onCheckedChange={(checked) => setData('is_featured', checked === true)}
                    />
                    <Label htmlFor="t-featured" className="font-normal">
                        Feature on the home page
                    </Label>
                </div>
            </div>
        </FormDialog>
    );
}
