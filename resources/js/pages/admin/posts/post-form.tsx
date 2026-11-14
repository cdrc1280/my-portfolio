import { Link } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { FormField } from '@/components/custom/form-field';
import { ImageUploadField } from '@/components/custom/image-upload-field';
import { TagPicker } from '@/components/custom/tag-picker';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import { usePostForm } from '@/hooks/use-post-form';
import { index } from '@/routes/admin/posts';
import type { SelectOption, Tag } from '@/types';
import type { AdminPost, PostStatus } from '@/types/domains/blog';

type PostFormProps = {
    post?: AdminPost;
    tags: Tag[];
    statuses: SelectOption<PostStatus>[];
};

export function PostForm({ post, tags, statuses }: PostFormProps) {
    const { form, submit } = usePostForm(post);
    const { data, setData, errors, processing } = form;

    const onSubmit = (event: FormEvent) => {
        event.preventDefault();
        submit();
    };

    return (
        <form onSubmit={onSubmit} className="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]" noValidate>
            <Card>
                <CardContent className="grid grid-cols-1 gap-5 pt-6">
                    <FormField required label="Title" htmlFor="title" error={errors.title}>
                        <Input
                            id="title"
                            value={data.title}
                            onChange={(e) => setData('title', e.target.value)}
                            autoFocus={!post}
                        />
                    </FormField>
                    <FormField
                        label="Slug"
                        htmlFor="slug"
                        error={errors.slug}
                        hint="Leave empty to generate it from the title."
                    >
                        <Input id="slug" value={data.slug} onChange={(e) => setData('slug', e.target.value)} />
                    </FormField>
                    <FormField
                        required
                        label="Excerpt"
                        htmlFor="excerpt"
                        error={errors.excerpt}
                        hint="Shown in lists and as the meta description."
                    >
                        <Textarea
                            id="excerpt"
                            rows={3}
                            value={data.excerpt}
                            onChange={(e) => setData('excerpt', e.target.value)}
                        />
                    </FormField>
                    <div className="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <FormField
                            label="SEO title"
                            htmlFor="seo_title"
                            error={errors.seo_title}
                            hint="Defaults to the title."
                        >
                            <Input
                                id="seo_title"
                                maxLength={70}
                                value={data.seo_title}
                                onChange={(e) => setData('seo_title', e.target.value)}
                            />
                        </FormField>
                        <FormField
                            label="SEO description"
                            htmlFor="seo_description"
                            error={errors.seo_description}
                            hint="Defaults to the excerpt."
                        >
                            <Input
                                id="seo_description"
                                maxLength={200}
                                value={data.seo_description}
                                onChange={(e) => setData('seo_description', e.target.value)}
                            />
                        </FormField>
                    </div>
                    <FormField
                        required
                        label="Body"
                        htmlFor="body"
                        error={errors.body}
                        hint="Markdown supported. Fenced code blocks (```php) are syntax-highlighted."
                    >
                        <Textarea
                            id="body"
                            rows={22}
                            className="font-mono text-sm"
                            value={data.body}
                            onChange={(e) => setData('body', e.target.value)}
                        />
                    </FormField>
                </CardContent>
            </Card>

            <aside className="grid min-w-0 grid-cols-1 content-start gap-6 lg:sticky lg:top-4">
                <Card>
                    <CardHeader>
                        <CardTitle>Publishing</CardTitle>
                    </CardHeader>
                    <CardContent className="grid grid-cols-1 gap-5">
                        <FormField required label="Status" htmlFor="status" error={errors.status}>
                            <Select
                                value={data.status}
                                onValueChange={(value) => setData('status', value as PostStatus)}
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
                        <FormField
                            label="Publish date"
                            htmlFor="published_at"
                            error={errors.published_at}
                            hint="Optional. A future date schedules the post."
                        >
                            <Input
                                id="published_at"
                                type="datetime-local"
                                value={data.published_at}
                                onChange={(e) => setData('published_at', e.target.value)}
                            />
                        </FormField>
                        <div className="flex gap-2">
                            <Button type="submit" disabled={processing} className="flex-1">
                                {processing && <Spinner />}
                                {post ? 'Save changes' : 'Create post'}
                            </Button>
                            <Button variant="outline" asChild>
                                <Link href={index()}>Cancel</Link>
                            </Button>
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader>
                        <CardTitle id="post-tags-label">Tags</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <TagPicker
                            tags={tags}
                            selected={data.tag_ids}
                            onChange={(ids) => setData('tag_ids', ids)}
                            labelledBy="post-tags-label"
                        />
                    </CardContent>
                </Card>
                <Card>
                    <CardContent className="pt-6">
                        <ImageUploadField
                            label="Cover image"
                            currentUrl={post?.cover_url ?? null}
                            file={data.cover}
                            removed={data.remove_cover}
                            onFileChange={(file) => setData('cover', file)}
                            onRemovedChange={(removed) => setData('remove_cover', removed)}
                            error={errors.cover}
                        />
                    </CardContent>
                </Card>
            </aside>
        </form>
    );
}
