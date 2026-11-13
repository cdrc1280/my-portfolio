import { useForm } from '@inertiajs/react';
import { succeeded } from '@/lib/form-result';
import { store, update } from '@/routes/admin/posts';
import type { AdminPost, PostStatus } from '@/types/domains/blog';

export type PostFormData = {
    title: string;
    slug: string;
    excerpt: string;
    seo_title: string;
    seo_description: string;
    body: string;
    status: PostStatus;
    /** Local `YYYY-MM-DDTHH:mm` for <input type="datetime-local">. */
    published_at: string;
    tag_ids: number[];
    cover: File | null;
    remove_cover: boolean;
};

function toLocalInput(iso: string | null): string {
    if (!iso) {
        return '';
    }

    const date = new Date(iso);
    const offset = date.getTimezoneOffset() * 60_000;

    return new Date(date.getTime() - offset).toISOString().slice(0, 16);
}

export function usePostForm(post?: AdminPost) {
    const form = useForm<PostFormData>({
        title: post?.title ?? '',
        slug: post?.slug ?? '',
        excerpt: post?.excerpt ?? '',
        seo_title: post?.seo_title ?? '',
        seo_description: post?.seo_description ?? '',
        body: post?.body ?? '',
        status: post?.status ?? 'draft',
        published_at: toLocalInput(post?.published_at ?? null),
        tag_ids: post?.tags.map((tag) => tag.id) ?? [],
        cover: null,
        remove_cover: false,
    });

    const submit = () => {
        // Send the publish date as an absolute ISO timestamp.
        const withIsoDate = (data: PostFormData) => ({
            ...data,
            published_at: data.published_at ? new Date(data.published_at).toISOString() : '',
        });

        if (post) {
            form.transform((data) => ({ ...withIsoDate(data), _method: 'put' }));
            form.post(update.url(post.id), {
                forceFormData: true,
                preserveScroll: true,
                onSuccess: (page) => succeeded(page) && form.reset('cover', 'remove_cover'),
            });

            return;
        }

        form.transform(withIsoDate);
        form.post(store.url(), { forceFormData: true });
    };

    return { form, submit };
}
