<h1><?= htmlspecialchars($heading) ?></h1>

<p>
    A second route, <code>/about</code>, handled by <code>AboutController</code>.
    It shares the same layout and the same bundled <code>main.ts</code> as the
    home page &mdash; one entry point, many server-rendered pages.
</p>

<p>
    Add a page by dropping a controller in <code>app/Controllers</code> and a
    line in the routing table.
</p>
