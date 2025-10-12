<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DocumentationController extends Controller
{
    public function index(?string $section = null, ?string $document = null): Response
    {
        return $this->render($section, $document);
    }

    public function section(string $section): Response
    {
        return $this->render($section);
    }

    public function show(string $section, string $document): Response
    {
        return $this->render($section, $document);
    }

    private function render(?string $sectionSlug = null, ?string $documentSlug = null): Response
    {
        $sections = $this->scanSections();

        if ($sections->isEmpty()) {
            return Inertia::render('docs/index', [
                'sections' => [],
                'current' => ['section' => null, 'document' => null],
                'document' => null,
            ]);
        }

        $sectionIndex = $sections->keyBy('slug');
        $activeSection = $sectionSlug ? $sectionIndex->get($sectionSlug) : $sections->first();

        if (! $activeSection) {
            abort(404);
        }

        $documentPayload = null;

        if ($documentSlug) {
            $documentRecord = collect($activeSection['files'])->firstWhere('slug', $documentSlug);

            if (! $documentRecord) {
                abort(404);
            }

            $documentPayload = [
                'slug' => $documentRecord['slug'],
                'title' => $documentRecord['title'],
                'html' => Str::markdown(File::get($documentRecord['path'])),
            ];
        }

        $sectionsForView = $sections->map(function ($section) {
            return [
                'slug' => $section['slug'],
                'title' => $section['title'],
                'summary' => $section['summary'],
                'fileCount' => $section['file_count'],
                'files' => collect($section['files'])
                    ->map(fn ($file) => Arr::only($file, ['slug', 'title', 'summary']))
                    ->values(),
            ];
        })->values();

        return Inertia::render('docs/index', [
            'sections' => $sectionsForView,
            'current' => [
                'section' => $activeSection['slug'],
                'document' => $documentPayload['slug'] ?? null,
            ],
            'document' => $documentPayload,
        ]);
    }

    private function scanSections()
    {
        $docsPath = base_path('.docs');

        if (! File::exists($docsPath)) {
            return collect();
        }

        return collect(File::directories($docsPath))
            ->sort()
            ->map(fn ($directory) => $this->makeSection($directory))
            ->filter(fn ($section) => $section['file_count'] > 0)
            ->values();
    }

    private function makeSection(string $directory): array
    {
        $folder = basename($directory);
        $title = $this->formatTitle($folder);
        $slug = Str::slug($title) ?: Str::slug($folder);

        $files = collect(File::files($directory))
            ->filter(fn ($file) => Str::lower($file->getExtension()) === 'md')
            ->map(fn ($file) => $this->makeDocument($file->getRealPath()))
            ->filter()
            ->values();

        return [
            'slug' => $slug,
            'title' => $title,
            'file_count' => $files->count(),
            'files' => $files->all(),
            'summary' => $files->first()['summary'] ?? null,
            'path' => $directory,
        ];
    }

    private function makeDocument(string $filePath): ?array
    {
        if (! File::exists($filePath)) {
            return null;
        }

        $filename = pathinfo($filePath, PATHINFO_FILENAME);
        $title = $this->formatTitle($filename);
        $slug = Str::slug($title) ?: Str::slug($filename);

        $content = Str::of(File::get($filePath))->replace(["\r\n", "\r"], "\n");
        $excerpt = $content
            ->split('/\n{2,}/')
            ->filter(fn ($chunk) => Str::of($chunk)->trim()->isNotEmpty())
            ->map(fn ($chunk) => Str::of($chunk)->replace('#', '')->trim())
            ->first();

        return [
            'slug' => $slug,
            'title' => $title,
            'summary' => optional($excerpt)->limit(140),
            'path' => $filePath,
        ];
    }

    private function formatTitle(string $value): string
    {
        return (string) Str::of($value)
            ->replace(['#', '_', '-'], ' ')
            ->replaceMatches('/\s+/', ' ')
            ->trim()
            ->ucfirst();
    }
}
