import { useForm } from '@inertiajs/react';
import type { AdminProject, ProjectStatus } from '@/features/projects/types';
import { store, update } from '@/routes/admin/projects';

export type ProjectFormData = {
    title: string;
    slug: string;
    summary: string;
    body: string;
    role: string;
    period: string;
    highlights: string[];
    live_url: string;
    repo_url: string;
    status: ProjectStatus;
    is_featured: boolean;
    tag_ids: number[];
    cover: File | null;
    remove_cover: boolean;
    images: File[];
    removed_image_ids: number[];
};

function initialData(project?: AdminProject): ProjectFormData {
    return {
        title: project?.title ?? '',
        slug: project?.slug ?? '',
        summary: project?.summary ?? '',
        body: project?.body ?? '',
        role: project?.role ?? '',
        period: project?.period ?? '',
        highlights: project?.highlights ?? [],
        live_url: project?.live_url ?? '',
        repo_url: project?.repo_url ?? '',
        status: project?.status ?? 'draft',
        is_featured: project?.is_featured ?? false,
        tag_ids: project?.tags.map((tag) => tag.id) ?? [],
        cover: null,
        remove_cover: false,
        images: [],
        removed_image_ids: [],
    };
}

/**
 * Create/edit form state for a project. Uploads are sent as multipart;
 * updates are POSTed with method spoofing because PHP only parses
 * multipart bodies on POST.
 */
export function useProjectForm(project?: AdminProject) {
    const form = useForm<ProjectFormData>(initialData(project));

    const submit = () => {
        if (project) {
            form.transform((data) => ({ ...data, _method: 'put' }));
            form.post(update.url(project.id), {
                forceFormData: true,
                preserveScroll: true,
                onSuccess: () => form.reset('cover', 'images', 'removed_image_ids', 'remove_cover'),
            });

            return;
        }

        form.post(store.url(), { forceFormData: true });
    };

    return { form, submit };
}
