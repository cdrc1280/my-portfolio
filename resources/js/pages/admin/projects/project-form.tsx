import { Link } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { FormField } from '@/components/custom/form-field';
import { ImageUploadField } from '@/components/custom/image-upload-field';
import InputError from '@/components/custom/input-error';
import { RepeatableRows } from '@/components/custom/repeatable-rows';
import { TagPicker } from '@/components/custom/tag-picker';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { useProjectForm } from '@/hooks/use-project-form';
import type { ProjectFormData } from '@/hooks/use-project-form';
import { index } from '@/routes/admin/projects';
import type { SelectOption, Tag } from '@/types';
import type { AdminProject, ProjectStatus } from '@/types/domains/projects';
import { GalleryField } from './gallery-field';

type ProjectFormProps = {
    project?: AdminProject;
    tags: Tag[];
    statuses: SelectOption<ProjectStatus>[];
};

type MarkdownKey = 'problem' | 'role_details' | 'architecture' | 'decisions' | 'challenges' | 'results';

/** Case-study editor: sections follow the order of the public page. */
export function ProjectForm({ project, tags, statuses }: ProjectFormProps) {
    const { form, submit } = useProjectForm(project);
    const { data, setData, errors, processing } = form;
    const errorBag = errors as Partial<Record<string, string>>;

    const onSubmit = (event: FormEvent) => {
        event.preventDefault();
        submit();
    };

    const galleryError =
        errors.images ??
        Object.entries(errors).find(([key]) => key.startsWith('images.'))?.[1] ??
        errors.removed_image_ids;

    const markdown = (key: MarkdownKey, label: string, hint: string, rows = 5) => (
        <FormField label={label} htmlFor={key} error={errors[key]} hint={hint}>
            <Textarea
                id={key}
                rows={rows}
                className="font-mono text-sm"
                value={data[key]}
                onChange={(event) => setData(key, event.target.value)}
            />
        </FormField>
    );

    const toggle = (
        key: keyof Pick<ProjectFormData, 'is_featured' | 'is_confidential'>,
        label: string,
        hint: string,
    ) => (
        <div className="flex items-center justify-between gap-4">
            <div>
                <Label htmlFor={key}>{label}</Label>
                <p className="text-xs text-muted-foreground">{hint}</p>
            </div>
            <Switch id={key} checked={data[key]} onCheckedChange={(checked) => setData(key, checked)} />
        </div>
    );

    return (
        <form onSubmit={onSubmit} className="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]" noValidate>
            <div className="grid min-w-0 grid-cols-1 content-start gap-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Header</CardTitle>
                        <CardDescription>Title, one-line summary, your role and dates.</CardDescription>
                    </CardHeader>
                    <CardContent className="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <FormField required label="Title" htmlFor="title" error={errors.title}>
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
                            hint="Leave empty to generate it from the title."
                        >
                            <Input
                                id="slug"
                                value={data.slug}
                                onChange={(event) => setData('slug', event.target.value)}
                            />
                        </FormField>
                        <FormField
                            required
                            label="Summary"
                            htmlFor="summary"
                            error={errors.summary}
                            className="sm:col-span-2"
                            hint="One line for cards and the page header (max 200 characters)."
                        >
                            <Input
                                id="summary"
                                value={data.summary}
                                maxLength={200}
                                onChange={(event) => setData('summary', event.target.value)}
                                required
                            />
                        </FormField>
                        <FormField
                            label="Your role"
                            htmlFor="role"
                            error={errors.role}
                            hint="e.g. Full-stack developer"
                        >
                            <Input
                                id="role"
                                value={data.role}
                                onChange={(event) => setData('role', event.target.value)}
                            />
                        </FormField>
                        <div className="grid grid-cols-2 gap-3">
                            <FormField label="Start" htmlFor="started_on" error={errors.started_on}>
                                <Input
                                    id="started_on"
                                    type="month"
                                    value={data.started_on}
                                    onChange={(event) => setData('started_on', event.target.value)}
                                />
                            </FormField>
                            <FormField label="End" htmlFor="ended_on" error={errors.ended_on} hint="Empty = ongoing">
                                <Input
                                    id="ended_on"
                                    type="month"
                                    value={data.ended_on}
                                    onChange={(event) => setData('ended_on', event.target.value)}
                                />
                            </FormField>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Case study</CardTitle>
                        <CardDescription>Markdown supported. Empty sections are hidden on the site.</CardDescription>
                    </CardHeader>
                    <CardContent className="grid grid-cols-1 gap-5">
                        {markdown('problem', 'Problem', 'What was broken, slow or missing, in the client’s terms.')}
                        {markdown('role_details', 'My role', 'What you personally owned. A bullet list works well.')}
                        {markdown('architecture', 'Architecture', 'How the system fits together.', 6)}
                        <FormField
                            label="Folder tree"
                            htmlFor="architecture_tree"
                            error={errors.architecture_tree}
                            hint="One entry per line, indent with spaces. End folders with / and add notes after #."
                        >
                            <Textarea
                                id="architecture_tree"
                                rows={8}
                                className="font-mono text-sm"
                                placeholder={'app/\n  Domain/  # business logic\n  Http/\ntests'}
                                value={data.architecture_tree}
                                onChange={(event) => setData('architecture_tree', event.target.value)}
                            />
                        </FormField>
                        <ImageUploadField
                            label="Architecture diagram"
                            currentUrl={project?.diagram_url ?? null}
                            file={data.diagram}
                            removed={data.remove_diagram}
                            onFileChange={(file) => setData('diagram', file)}
                            onRemovedChange={(removed) => setData('remove_diagram', removed)}
                            error={errors.diagram}
                        />
                        {markdown('decisions', 'Key decisions', 'Bold the decision, then explain the trade-off.', 6)}
                        {markdown('challenges', 'Challenges & solutions', 'What got in the way and how you solved it.')}
                        {markdown('results', 'Results', 'Outcomes you can stand behind.')}
                        <RepeatableRows
                            label="Metrics"
                            hint="Short, verifiable numbers, e.g. Load time / -60%."
                            rows={data.metrics}
                            columns={[
                                { key: 'label', label: 'Label', placeholder: 'Load time' },
                                { key: 'value', label: 'Value', placeholder: '-60%' },
                            ]}
                            empty={{ label: '', value: '' }}
                            onChange={(rows) => setData('metrics', rows)}
                            errors={errorBag}
                            errorKey="metrics"
                            addLabel="Add metric"
                        />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Links</CardTitle>
                    </CardHeader>
                    <CardContent className="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <FormField label="Live URL" htmlFor="live_url" error={errors.live_url}>
                            <Input
                                id="live_url"
                                type="url"
                                inputMode="url"
                                value={data.live_url}
                                onChange={(event) => setData('live_url', event.target.value)}
                            />
                        </FormField>
                        <FormField
                            label="Repository URL"
                            htmlFor="repo_url"
                            error={errors.repo_url}
                            hint={
                                data.is_confidential ? 'Hidden publicly while the project is confidential.' : undefined
                            }
                        >
                            <Input
                                id="repo_url"
                                type="url"
                                inputMode="url"
                                value={data.repo_url}
                                onChange={(event) => setData('repo_url', event.target.value)}
                            />
                        </FormField>
                        <FormField
                            label="Demo video / GIF URL"
                            htmlFor="demo_url"
                            error={errors.demo_url}
                            className="sm:col-span-2"
                        >
                            <Input
                                id="demo_url"
                                type="url"
                                inputMode="url"
                                value={data.demo_url}
                                onChange={(event) => setData('demo_url', event.target.value)}
                            />
                        </FormField>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Media</CardTitle>
                    </CardHeader>
                    <CardContent className="grid grid-cols-1 gap-6">
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

                <Card>
                    <CardHeader>
                        <CardTitle>SEO</CardTitle>
                        <CardDescription>Optional. Falls back to the title and summary.</CardDescription>
                    </CardHeader>
                    <CardContent className="grid grid-cols-1 gap-5">
                        <FormField
                            label="SEO title"
                            htmlFor="seo_title"
                            error={errors.seo_title}
                            hint="Up to 70 characters."
                        >
                            <Input
                                id="seo_title"
                                maxLength={70}
                                value={data.seo_title}
                                onChange={(event) => setData('seo_title', event.target.value)}
                            />
                        </FormField>
                        <FormField
                            label="SEO description"
                            htmlFor="seo_description"
                            error={errors.seo_description}
                            hint="Up to 200 characters."
                        >
                            <Textarea
                                id="seo_description"
                                rows={2}
                                maxLength={200}
                                value={data.seo_description}
                                onChange={(event) => setData('seo_description', event.target.value)}
                            />
                        </FormField>
                    </CardContent>
                </Card>
            </div>

            <aside className="grid min-w-0 grid-cols-1 content-start gap-6 lg:sticky lg:top-4">
                <Card>
                    <CardHeader>
                        <CardTitle>Publishing</CardTitle>
                    </CardHeader>
                    <CardContent className="grid grid-cols-1 gap-5">
                        <FormField required label="Status" htmlFor="status" error={errors.status}>
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
                        {toggle('is_featured', 'Featured', 'Shown on the home page (first three).')}
                        {toggle('is_confidential', 'Confidential', 'Hides the repo and shows “demo on request”.')}
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
                    <CardContent className="grid grid-cols-1 gap-2">
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
