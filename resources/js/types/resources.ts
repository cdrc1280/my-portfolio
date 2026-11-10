/*
 * Types shared across features. Each mirrors a Laravel API Resource exactly;
 * the PHP class is named in the doc comment so the two can be kept in sync.
 */

/** App\Http\Resources\TagResource */
export type Tag = {
    id: number;
    name: string;
    slug: string;
};

/** Enum options produced by the backend (e.g. ProjectStatus::options()). */
export type SelectOption<TValue extends string = string> = {
    value: TValue;
    label: string;
};
