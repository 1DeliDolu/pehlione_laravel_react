# Docs Module Progress

## What Changed
- Added `app/Http/Controllers/DocumentationController.php` to scan `.docs/` directories, slugify folder/file names, and render Markdown via Inertia. It returns section lists, document summaries, and the HTML for the selected file.
- Registered a `/docs` route group in `routes/web.php` that serves the docs index (`/docs`), sections (`/docs/{section}`), and documents (`/docs/{section}/{document}`) through the new controller.
- Created `resources/js/pages/docs/index.tsx`, which renders:
  - Section cards laid out in a responsive grid.
  - Per-section note cards that open the markdown article.
  - A markdown preview panel with responsive typography, modern heading styles, and break-word handling.
- Updated navigation so users can reach the docs UI from both the public navbar (`SiteNavbar`) and the authenticated sidebar footer (`NavFooter`, `AppSidebar`).
- Refined the markdown container: long words wrap, tables scroll horizontally, fenced code blocks have reduced padding and vertical margin, and headings gain contemporary spacing so the page reads like modern documentation.

## Key Snippets
### Controller: `DocumentationController`
```php
$documentPayload = [
    'slug' => $documentRecord['slug'],
    'title' => $documentRecord['title'],
    'html' => Str::markdown(File::get($documentRecord['path'])),
];

return Inertia::render('docs/index', [
    'sections' => $sectionsForView,
    'current' => [
        'section' => $activeSection['slug'],
        'document' => $documentPayload['slug'] ?? null,
    ],
    'document' => $documentPayload,
]);
```

### React Page Styling: `resources/js/pages/docs/index.tsx`
```tsx
<div
    className="docs-content max-w-full break-words text-sm leading-relaxed text-neutral-700
        dark:text-neutral-200 [&_a]:text-amber-600 [&_a]:underline [&_a]:underline-offset-4
        [&_code]:rounded [&_code]:bg-neutral-100 [&_code]:px-1 [&_code]:py-0.5 [&_code]:text-[0.85em]
        [&_h1]:mt-8 [&_h1]:text-3xl [&_h1]:font-semibold [&_h1]:tracking-tight
        [&_h2]:mt-6 [&_h2]:text-2xl [&_h2]:font-semibold [&_li]:break-words [&_p]:break-words
        [&_pre]:my-6 [&_pre]:overflow-x-auto [&_pre]:rounded-xl [&_pre]:border [&_pre]:border-neutral-200
        [&_pre]:bg-neutral-100 [&_pre]:px-3 [&_pre]:py-3 [&_pre]:font-mono [&_pre]:text-xs md:[&_pre]:text-sm
        [&_table]:block [&_table]:max-w-full [&_table]:overflow-x-auto
        dark:[&_a]:text-amber-300 dark:[&_code]:bg-neutral-800 dark:[&_pre]:border-neutral-700
        dark:[&_pre]:bg-neutral-900"
    dangerouslySetInnerHTML={{ __html: document.html }}
/>
```

### Routes: `routes/web.php`
```php
Route::prefix('docs')->group(function () {
    Route::get('/', [DocumentationController::class, 'index'])->name('docs.index');
    Route::get('{section}', [DocumentationController::class, 'section'])->name('docs.section');
    Route::get('{section}/{document}', [DocumentationController::class, 'show'])->name('docs.show');
});
```

## Usage Tips
- Place Markdown notes under `.docs/<Section>/<name>.md`. The controller slugifies both folder and file names automatically.
- To link to docs in React, import helpers from `@/routes/docs` (e.g., `import { index as docsIndex } from '@/routes/docs';`).
- Markdown headings and code blocks already adopt modern spacing — add more Tailwind utilities via `.docs-content` if you need bespoke typography or layout tweaks.
