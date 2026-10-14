<?php

declare(strict_types=1);

/**
 * Base controller. Gives every controller a render() helper that runs a view
 * template and drops its output into the shared layout.
 */
abstract class Controller
{
    /**
     * @param string               $view Template name in app/Views (no extension)
     * @param array<string, mixed> $data Variables exposed to the view + layout
     */
    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        // Render the page body into a string...
        ob_start();
        require __DIR__ . '/Views/' . $view . '.php';
        $content = ob_get_clean();

        // ...then wrap it in the layout. The layout echoes $content (and can
        // read anything from $data, e.g. $title).
        require __DIR__ . '/Views/layout.php';
    }
}
