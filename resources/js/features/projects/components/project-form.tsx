import { Link } from '@inertiajs/react';
import type { FormEvent } from 'react';
import InputError from '@/components/input-error';
import { FormField } from '@/components/shared/form-field';
import { ImageUploadField } from '@/components/shared/image-upload-field';
import { TagPicker } from '@/components/shared/tag-picker';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { GalleryField } from '@/features/projects/components/gallery-field';
import { useProjectForm } from '@/features/projects/hooks/use-project-form';
import type { AdminProject, ProjectStatus } from '@/features/projects/types';
import { index } from '@/routes/admin/projects';
import type { SelectOption, Tag } from '@/types';

type ProjectFormProps = {
    project?: AdminProject;
    tags: Tag[];
    statuses: SelectOption<ProjectStatus>[];
};

export function ProjectForm({ project, tags, statuses }: ProjectFormProps) {
    const { form, submit } = useProjectForm(project);
    const { data, setData, errors, processing } = form;

    const onSubmit = (event: FormEvent) => {
        event.preventDefault();
        submit();
    };

    const galleryError =
        errors.images ??
        Object.entries(errors).find(([key]) => key.startsWith('images.'))?.[1] ??
        errors.removed_image_ids;

    return (
        <form onSubmit={onSubmit} className="grid gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]" noValidate>
            <div className="grid content-start gap-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Content</CardTitle>
                    </CardHeader>
                    <CardContent className="grid gap-5">
                        <FormField label="Title" htmlFor="title" error={errors.title}>
                            <Input
                                id="title"
                                value={data.title}
                                onChange={(event) => setData('title', event.target.value)}
                                required
                                autoFocus={!project}
                            />
                        </FormField>
                        <FormField
                            label="Slug"
                            htmlFor="slug"
                            error={errors.slug}
                            hint="Used in the URL. Leave empty to generate it from the title."
                        >
                            <Input
                                id="slug"
                                value={data.slug}
                                onChange={(event) => setData('slug', event.target.value)}
                            />
                        </FormField>
                        <FormField
                            label="Summary"
                            htmlFor="summary"
                            error={errors.summary}
                            hint="One or two sentences for cards and search results."
                        >
                            <Textarea
                                id="summary"
                                rows={3}
                                value={data.summary}
                                onChange={(event) => setData('summary', event.target.value)}
                                required
                            />
                        </FormField>
                        <FormField label="Case study" htmlFor="body" error={errors.body} hint="Markdown supported.">
                            <Textarea
                                id="body"
                                rows={14}
                                className="font-mono text-sm"
                                value={data.body}
                                onChange={(event) => setData('body', event.target.value)}
                            />
                        </FormField>
                        <FormField
                            label="Highlights"
                            htmlFor="highlights"
                            error={errors.highlights}
                            hint="One per line."
                        >
                            <Textarea
                                id="highlights"
                                rows={5}
                                value={data.highlights.join('\n')}
                                onChange={(event) => setData('highlights', event.target.value.split('\n'))}
                            />
                        </FormField>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Details</CardTitle>
                    </CardHeader>
                    <CardContent className="grid gap-5 sm:grid-cols-2">
                        <FormField label="Your role" htmlFor="role" error={errors.role}>
                            <Input
                                id="role"
                                value={data.role}
                                onChange={(event) => setData('role', event.target.value)}
                            />
                        </FormField>
                        <FormField label="Period" htmlFor="period" error={errors.period} hint="e.g. 2023 – 2025">
                            <Input
                                id="period"
                                value={data.period}
                                onChange={(event) => setData('period', event.target.value)}
                            />
                        </FormField>
                        <FormField label="Live URL" htmlFor="live_url" error={errors.live_url}>
                            <Input
                                id="live_url"
                                type="url"
                                inputMode="url"
                                value={data.live_url}
                                onChange={(event) => setData('live_url', event.target.value)}
                            />
                        </FormField>
                        <FormField label="Repository URL" htmlFor="repo_url" error={errors.repo_url}>
                            <Input
                                id="repo_url"
                                type="url"
                                inputMode="url"
                                value={data.repo_url}
                                onChange={(event) => setData('repo_url', event.target.value)}
                            />
                        </FormField>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Media</CardTitle>
                    </CardHeader>
                    <CardContent className="grid gap-6">
                        <ImageUploadField
                            label="Cover image"
                            currentUrl={project?.cover_url ?? null}
                            file={data.cover}
                            removed={data.remove_cover}
                            onFileChange={(file) => setData('cover', file)}
                            onRemovedChange={(removed) => setData('remove_cover', removed)}
                            error={errors.cover}
                        />
                        <GalleryField
                            existing={project?.images ?? []}
                            removedIds={data.removed_image_ids}
                            newFiles={data.images}
                            onRemovedIdsChange={(ids) => setData('removed_image_ids', ids)}
                            onNewFilesChange={(files) => setData('images', files)}
                            error={galleryError}
                        />
                    </CardContent>
                </Card>
            </div>

            <aside className="grid content-start gap-6 lg:sticky lg:top-4">
                <Card>
                    <CardHeader>
                        <CardTitle>Publishing</CardTitle>
                    </CardHeader>
                    <CardContent className="grid gap-5">
                        <FormField label="Status" htmlFor="status" error={errors.status}>
                            <Select
                                value={data.status}
                                onValueChange={(value) => setData('status', value as ProjectStatus)}
                            >
                                <SelectTrigger id="status">
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    {statuses.map((status) => (
                                        <SelectItem key={status.value} value={status.value}>
                                            {status.label}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </FormField>
                        <div className="flex items-center justify-between gap-4">
                            <div>
                                <Label htmlFor="is_featured">Featured</Label>
                                <p className="text-xs text-muted-foreground">Shown on the home page.</p>
                            </div>
                            <Switch
                                id="is_featured"
                                checked={data.is_featured}
                                onCheckedChange={(checked) => setData('is_featured', checked)}
                            />
                        </div>
                        <div className="flex gap-2">
                            <Button type="submit" disabled={processing} className="flex-1">
                                {processing && <Spinner />}
                                {project ? 'Save changes' : 'Create project'}
                            </Button>
                            <Button variant="outline" asChild>
                                <Link href={index()}>Cancel</Link>
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle id="tags-label">Technologies</CardTitle>
                    </CardHeader>
                    <CardContent className="grid gap-2">
                        <TagPicker
                            tags={tags}
                            selected={data.tag_ids}
                            onChange={(ids) => setData('tag_ids', ids)}
                            labelledBy="tags-label"
                        />
                        <InputError message={errors.tag_ids} />
                    </CardContent>
                </Card>
            </aside>
        </form>
    );
}
