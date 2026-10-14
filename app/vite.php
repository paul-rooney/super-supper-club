<?php

declare(strict_types=1);

/**
 * The one piece of glue between PHP and Vite.
 *
 *   Dev  (APP_ENV=development): point the browser at the Vite dev server so you
 *                               get HMR and on-the-fly TypeScript compilation.
 *   Prod (anything else):       read public/dist/.vite/manifest.json and emit
 *                               hashed, cache-busted <link>/<script> tags.
 */

const VITE_DEV_SERVER = 'http://localhost:5173';
const VITE_MANIFEST   = __DIR__ . '/../public/dist/.vite/manifest.json';
const VITE_ASSET_BASE = '/dist/'; // must match `base` in vite.config.ts (build)

function vite_is_dev(): bool
{
    return ($_SERVER['APP_ENV'] ?? getenv('APP_ENV')) === 'development';
}

/**
 * Return the HTML tags that load a Vite entry point (e.g. "src/main.ts").
 * Echo the result inside <head>.
 */
function vite(string $entry): string
{
    if (vite_is_dev()) {
        // @vite/client (the HMR runtime) must come before the entry.
        return sprintf(
            '<script type="module" src="%1$s/@vite/client"></script>' . "\n" .
            '<script type="module" src="%1$s/%2$s"></script>',
            VITE_DEV_SERVER,
            $entry,
        );
    }

    // ---- Production: resolve real (hashed) filenames from the manifest. ----
    static $manifest = null;
    if ($manifest === null) {
        if (!is_file(VITE_MANIFEST)) {
            throw new RuntimeException(
                'Vite manifest missing. Run "npm run build", '
                . 'or set APP_ENV=development to use the dev server.'
            );
        }
        $manifest = json_decode(
            (string) file_get_contents(VITE_MANIFEST),
            true,
            flags: JSON_THROW_ON_ERROR,
        );
    }

    if (!isset($manifest[$entry])) {
        throw new RuntimeException("Entry \"{$entry}\" not found in Vite manifest.");
    }

    $chunk = $manifest[$entry];
    $tags  = [];

    // 1. CSS the entry imports directly.
    foreach ($chunk['css'] ?? [] as $file) {
        $tags[] = '<link rel="stylesheet" href="' . VITE_ASSET_BASE . $file . '">';
    }
    // 2. CSS from shared chunks the entry statically imports.
    //    (One level deep covers the common "shared vendor chunk" case. For deep
    //     dependency graphs, walk `imports` recursively per the Vite docs.)
    foreach ($chunk['imports'] ?? [] as $import) {
        foreach ($manifest[$import]['css'] ?? [] as $file) {
            $tags[] = '<link rel="stylesheet" href="' . VITE_ASSET_BASE . $file . '">';
        }
    }
    // 3. The entry module itself.
    $tags[] = '<script type="module" src="' . VITE_ASSET_BASE . $chunk['file'] . '"></script>';

    return implode("\n", $tags);
}
