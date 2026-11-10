import { ExternalLink } from 'lucide-react';
import type { Certificate } from '@/features/experience/types';
import { formatMonth } from '@/lib/format';

export function CertificateList({ certificates }: { certificates: Certificate[] }) {
    return (
        <ul className="divide-y rounded-xl border">
            {certificates.map((certificate) => (
                <li key={certificate.id} className="flex items-center justify-between gap-4 px-4 py-3">
                    <div className="min-w-0">
                        <p className="font-medium">
                            {certificate.credential_url ? (
                                <a
                                    href={certificate.credential_url}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="inline-flex items-center gap-1.5 hover:text-brand"
                                >
                                    {certificate.name}
                                    <ExternalLink className="size-3.5" aria-label="(opens credential)" />
                                </a>
                            ) : (
                                certificate.name
                            )}
                        </p>
                        <p className="text-sm text-muted-foreground">{certificate.issuer}</p>
                    </div>
                    <p className="shrink-0 text-sm text-muted-foreground tabular-nums">
                        {formatMonth(certificate.issued_on)}
                    </p>
                </li>
            ))}
        </ul>
    );
}
