import SiteLayout from '@/layouts/site-layout';
import { cn } from '@/lib/utils';
import { section as docsSection, show as docsShow } from '@/routes/docs';
import { Head, Link } from '@inertiajs/react';

type Nullable<T> = T | null | undefined;

interface DocumentSummary {
    slug: string;
    title: string;
    summary?: string | null;
}

interface SectionSummary {
    slug: string;
    title: string;
    summary?: string | null;
    fileCount: number;
    files: DocumentSummary[];
}

interface DocumentPayload {
    slug: string;
    title: string;
    html: string;
}

interface DocsPageProps {
    sections: SectionSummary[];
    current: {
        section: Nullable<string>;
        document: Nullable<string>;
    };
    document?: Nullable<DocumentPayload>;
    access?: {
        restricted: boolean;
    };
}

export default function DocsIndex({ sections, current, document, access }: DocsPageProps) {
    const hasSections = sections.length > 0;
    const activeSectionSlug = current.section ?? sections[0]?.slug ?? null;
    const activeSection = sections.find((section) => section.slug === activeSectionSlug);
    const activeFiles = activeSection?.files ?? [];
    const restricted = access?.restricted ?? false;

    return (
        <SiteLayout>
            <Head title="Documentation" />
            <section className="space-y-10">
                <header className="space-y-3">
                    <p className="text-xs font-semibold uppercase tracking-[0.2em] text-amber-600 dark:text-amber-300">
                        Knowledge Base
                    </p>
                    <div className="flex flex-wrap items-end justify-between gap-3">
                        <h1 className="text-3xl font-semibold tracking-tight text-neutral-900 dark:text-neutral-50">
                            Internal Documentation
                        </h1>
                        {activeSection && (
                            <span className="text-sm text-neutral-500 dark:text-neutral-400">
                                {activeSection.fileCount}{' '}
                                note{activeSection.fileCount === 1 ? '' : 's'} in {activeSection.title}
                            </span>
                        )}
                    </div>
                    <p className="max-w-2xl text-sm text-neutral-600 dark:text-neutral-300">
                        Browse the living documentation stored under <code>.docs/</code>. Choose a section to reveal its Markdown notes, then open a note to render the formatted content inline.
                    </p>
                </header>

                {!hasSections && (
                    <div className="rounded-2xl border border-dashed border-neutral-300 bg-white p-8 text-center text-sm text-neutral-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-400">
                        {restricted ? (
                            <span>
                                No documentation is available for your role yet. Contact an administrator if you need additional
                                access.
                            </span>
                        ) : (
                            <span>
                                No documentation folders were detected. Add Markdown files under <code>.docs/</code> to populate this
                                view.
                            </span>
                        )}
                    </div>
                )}

                {hasSections && (
                    <div className="space-y-8">
                        <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                            {sections.map((section) => {
                                const isActive = section.slug === activeSectionSlug;
                                return (
                                    <Link
                                        key={section.slug}
                                        href={docsSection({ section: section.slug })}
                                        prefetch
                                        className={cn(
                                            'relative flex h-full flex-col justify-between overflow-hidden rounded-2xl border bg-white p-5 shadow-sm transition hover:border-amber-500/80 hover:shadow-md dark:bg-neutral-900',
                                            isActive
                                                ? 'border-amber-500 text-neutral-900 dark:border-amber-400 dark:text-neutral-100'
                                                : 'border-neutral-200 text-neutral-700 dark:border-neutral-700 dark:text-neutral-200',
                                        )}
                                    >
                                        <div className="space-y-3">
                                            <h2 className="break-words text-lg font-semibold leading-tight">
                                                {section.title}
                                            </h2>
                                            <p className="break-words text-sm text-neutral-500 dark:text-neutral-400">
                                                {section.summary ?? 'Select to view its notes.'}
                                            </p>
                                        </div>
                                        <span className="mt-6 inline-flex items-center text-xs font-semibold uppercase tracking-[0.2em] text-neutral-400 dark:text-neutral-500">
                                            {section.fileCount} file{section.fileCount === 1 ? '' : 's'}
                                        </span>
                                    </Link>
                                );
                            })}
                        </div>

                        <div className="space-y-6">
                            <div className="flex flex-wrap items-center justify-between gap-3">
                                <h2 className="text-xl font-semibold text-neutral-900 dark:text-neutral-50">
                                    {activeSection ? activeSection.title : 'Section'}
                                </h2>
                                <span className="text-xs text-neutral-500 dark:text-neutral-400">
                                    Choose a note to render its Markdown below.
                                </span>
                            </div>

                            {activeFiles.length === 0 ? (
                                <div className="rounded-2xl border border-dashed border-neutral-300 bg-white p-6 text-sm text-neutral-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-400">
                                    This section does not contain Markdown files yet.
                                </div>
                            ) : (
                                <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                                    {activeFiles.map((file) => {
                                        const isActive = current.document === file.slug;
                                        const sectionSlug = activeSection?.slug ?? activeSectionSlug ?? file.slug;

                                        return (
                                            <Link
                                                key={`${sectionSlug}-${file.slug}`}
                                                href={docsShow({
                                                    section: sectionSlug,
                                                    document: file.slug,
                                                })}
                                                prefetch
                                                className={cn(
                                                    'flex h-full flex-col justify-between overflow-hidden rounded-2xl border bg-white p-5 text-left shadow-sm transition hover:border-amber-500/80 hover:shadow-md dark:bg-neutral-900',
                                                    isActive
                                                        ? 'border-amber-500 text-neutral-900 dark:border-amber-400 dark:text-neutral-100'
                                                        : 'border-neutral-200 text-neutral-700 dark:border-neutral-700 dark:text-neutral-200',
                                                )}
                                            >
                                                <div className="space-y-3">
                                                    <h3 className="break-words text-base font-semibold leading-tight">
                                                        {file.title}
                                                    </h3>
                                                    {file.summary && (
                                                        <p className="break-words text-sm text-neutral-500 dark:text-neutral-400">
                                                            {file.summary}
                                                        </p>
                                                    )}
                                                </div>
                                                <span className="mt-6 inline-flex items-center text-xs font-semibold uppercase tracking-[0.2em] text-amber-600 dark:text-amber-300">
                                                    Open
                                                </span>
                                            </Link>
                                        );
                                    })}
                                </div>
                            )}
                        </div>

                        <div className="rounded-2xl border border-neutral-200 bg-white p-8 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                            {document ? (
                                <article className="space-y-6">
                                    <div className="space-y-2">
                                        <h2 className="break-words text-2xl font-semibold tracking-tight text-neutral-900 dark:text-neutral-100">
                                            {document.title}
                                        </h2>
                                        <p className="text-sm text-neutral-500 dark:text-neutral-400">
                                            Markdown rendered from <code>.docs</code>.
                                        </p>
                                    </div>
                                    <div
                                        className="docs-content max-w-full break-words text-sm leading-relaxed text-neutral-700 dark:text-neutral-200 [&_a]:text-amber-600 [&_a]:underline [&_a]:underline-offset-4 [&_code]:rounded [&_code]:bg-neutral-100 [&_code]:px-1 [&_code]:py-0.5 [&_code]:text-[0.85em] [&_h1]:mt-8 [&_h1]:text-3xl [&_h1]:font-semibold [&_h1]:tracking-tight [&_h2]:mt-6 [&_h2]:text-2xl [&_h2]:font-semibold [&_li]:break-words [&_p]:break-words [&_pre]:my-6 [&_pre]:overflow-x-auto [&_pre]:rounded-xl [&_pre]:border [&_pre]:border-neutral-200 [&_pre]:bg-neutral-100 [&_pre]:px-3 [&_pre]:py-3 [&_pre]:font-mono [&_pre]:text-xs md:[&_pre]:text-sm [&_table]:block [&_table]:max-w-full [&_table]:overflow-x-auto dark:[&_a]:text-amber-300 dark:[&_code]:bg-neutral-800 dark:[&_pre]:border-neutral-700 dark:[&_pre]:bg-neutral-900"
                                        dangerouslySetInnerHTML={{ __html: document.html }}
                                    />
                                </article>
                            ) : (
                                <div className="text-sm text-neutral-500 dark:text-neutral-400">
                                    Select a note to display its Markdown content.
                                </div>
                            )}
                        </div>
                    </div>
                )}
            </section>
        </SiteLayout>
    );
}


