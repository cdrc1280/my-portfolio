import { useForm } from '@inertiajs/react';
import { succeeded } from '@/lib/form-result';
import { store, update } from '@/routes/admin/projects';
import type { AdminProject, ProjectMetric, ProjectStatus } from '@/types/domains/projects';

export type ProjectFormData = {
    title: string;
    slug: string;
    summary: string;
    problem: string;
    role: string;
    role_details: string;
    architecture: string;
    architecture_tree: string;
    decisions: string;
    challenges: string;
    results: string;
    metrics: ProjectMetric[];
    live_url: string;
    repo_url: string;
    demo_url: string;
    is_confidential: boolean;
    started_on: string;
    ended_on: string;
    status: ProjectStatus;
    is_featured: boolean;
    seo_title: string;
    seo_description: string;
    tag_ids: number[];
    cover: File | null;
    remove_cover: boolean;
    diagram: File | null;
    remove_diagram: boolean;
    images: File[];
    removed_image_ids: number[];
};

function initialData(project?: AdminProject): ProjectFormData {
    return {
        title: project?.title ?? '',
        slug: project?.slug ?? '',
        summary: project?.summary ?? '',
        problem: project?.problem ?? '',
        role: project?.role ?? '',
        role_details: project?.role_details ?? '',
        architecture: project?.architecture ?? '',
        architecture_tree: project?.architecture_tree ?? '',
        decisions: project?.decisions ?? '',
        challenges: project?.challenges ?? '',
        results: project?.results ?? '',
        metrics: project?.metrics ?? [],
        live_url: project?.live_url ?? '',
        repo_url: project?.repo_url ?? '',
        demo_url: project?.demo_url ?? '',
        is_confidential: project?.is_confidential ?? false,
        started_on: project?.started_on ?? '',
        ended_on: project?.ended_on ?? '',
        status: project?.status ?? 'draft',
        is_featured: project?.is_featured ?? false,
        seo_title: project?.seo_title ?? '',
        seo_description: project?.seo_description ?? '',
        tag_ids: project?.tags.map((tag) => tag.id) ?? [],
        cover: null,
        remove_cover: false,
        diagram: null,
        remove_diagram: false,
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
                onSuccess: (page) =>
                    succeeded(page) &&
                    form.reset('cover', 'images', 'removed_image_ids', 'remove_cover', 'diagram', 'remove_diagram'),
            });

            return;
        }

        form.post(store.url(), { forceFormData: true });
    };

    return { form, submit };
}
