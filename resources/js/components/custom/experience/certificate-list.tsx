import { ExternalLink } from 'lucide-react';
import { LoadingImage } from '@/components/custom/loading-image';
import { formatMonth } from '@/lib/format';
import type { Certificate } from '@/types/domains/experience';

export function CertificateList({ certificates }: { certificates: Certificate[] }) {
    return (
        <ul className="divide-y divide-border rounded-lg border border-border bg-card">
            {certificates.map((certificate) => (
                <li key={certificate.id} className="flex items-center justify-between gap-4 px-4 py-3">
                    {certificate.badge_url && (
                        <LoadingImage
                            src={certificate.badge_url}
                            alt=""
                            width={40}
                            height={40}
                            frameClassName="size-10 shrink-0 rounded-md border border-border"
                            className="object-contain"
                        />
                    )}
                    <div className="min-w-0 flex-1">
                        <p className="font-medium text-foreground">
                            {certificate.credential_url ? (
                                <a
                                    href={certificate.credential_url}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="inline-flex items-center gap-1.5 rounded-sm transition-colors duration-150 hover:text-primary"
                                >
                                    {certificate.name}
                                    <ExternalLink className="size-3.5" aria-hidden />
                                    <span className="sr-only">(opens credential in a new tab)</span>
                                </a>
                            ) : (
                                certificate.name
                            )}
                        </p>
                        {certificate.issuer && <p className="text-sm">{certificate.issuer}</p>}
                    </div>
                    <p className="shrink-0 font-mono text-xs">{formatMonth(certificate.issued_on)}</p>
                </li>
            ))}
        </ul>
    );
}
