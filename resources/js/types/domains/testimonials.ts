/** App\Domains\*\Resources\TestimonialResource */
export type Testimonial = {
    id: number;
    author_name: string;
    author_role: string | null;
    company: string | null;
    photo_url: string | null;
    quote: string;
    project?: { title: string; slug: string } | null;
};

/** App\Domains\*\Resources\AdminTestimonialResource */
export type AdminTestimonial = Testimonial & {
    project_id: number | null;
    has_permission: boolean;
    is_featured: boolean;
};
