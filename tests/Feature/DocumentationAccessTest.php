<?php

use App\Enums\Role;
use App\Models\User;

$projectRoot = dirname(__DIR__, 2);
$docsDir = null;
$docsFile = null;
$wasDirectoryCreated = false;

beforeAll(function () use (&$wasDirectoryCreated, &$docsDir, &$docsFile, $projectRoot): void {
    $docsDir = $projectRoot.'/.docs/Vertrieb';
    $docsFile = $docsDir.'/0_role_test.md';

    if (! is_dir($docsDir)) {
        mkdir($docsDir, 0755, true);
        $wasDirectoryCreated = true;
    }

    file_put_contents($docsFile, "# Vertrieb Test\n\nRestricted content for the sales team.");
});

afterAll(function () use (&$wasDirectoryCreated, &$docsDir, &$docsFile): void {
    if ($docsFile && file_exists($docsFile)) {
        unlink($docsFile);
    }

    if ($wasDirectoryCreated && $docsDir && is_dir($docsDir)) {
        rmdir($docsDir);
    }
});

it('allows Vertrieb users to view the Vertrieb documentation section', function (): void {
    $user = User::factory()->create([
        'role' => Role::VERTRIEB,
    ]);

    $response = $this->actingAs($user)->get('/docs/vertrieb');

    $response->assertOk();
});

it('blocks other departments from accessing the Vertrieb section', function (): void {
    $user = User::factory()->create([
        'role' => Role::MARKETING,
    ]);

    $response = $this->actingAs($user)->get('/docs/vertrieb');

    $response->assertForbidden();
});

it('redirects guests to the login page when requesting docs', function (): void {
    $response = $this->get('/docs');

    $response->assertRedirect(route('login'));
});
