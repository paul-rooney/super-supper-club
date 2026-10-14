# Minimal Vite + TypeScript + vanilla PHP MVC

A tiny multi-page app where **PHP owns the HTML/routing (MVC)** and **Vite owns the
frontend** (TypeScript bundling, HMR in dev, hashed + manifested assets in prod).
The only npm dependencies are `vite` and `typescript`. No frameworks, no Composer.

## How the two halves connect

PHP renders every page. The layout calls one helper — `vite('src/main.ts')` — which:

- **in dev** (`APP_ENV=development`): prints `<script>` tags pointing at the Vite dev
  server (`http://localhost:5173`), giving you HMR and on-the-fly TS compilation.
- **in prod** (default): reads `public/dist/.vite/manifest.json` and prints the
  correct hashed `<link>`/`<script>` tags.

That's the entire integration. See `app/vite.php`.

```
├── package.json          # deps: vite + typescript only
├── tsconfig.json
├── vite.config.ts        # asset pipeline: manifest + single entry, pinned dev port
│
├── src/                  # the ONLY frontend source Vite/HMR manages
│   ├── main.ts           #   single entry point, shared by every page
│   └── style.css
│
├── app/                  # PHP application (NOT web-accessible)
│   ├── bootstrap.php     #   requires + routing table + dispatch
│   ├── vite.php          #   the dev/prod asset helper  ← the bridge
│   ├── Router.php
│   ├── Controller.php    #   base controller: render() wraps a view in the layout
│   ├── Controllers/
│   │   ├── HomeController.php
│   │   └── AboutController.php
│   └── Views/
│       ├── layout.php    #   shared shell; calls vite()
│       ├── home.php
│       └── about.php
│
└── public/               # web root — the only publicly served folder
    ├── index.php         #   front controller
    ├── .htaccess         #   Apache: route non-files to index.php
    └── dist/             #   Vite build output (generated; gitignored)
```

Views and app code live **outside** `public/`, so only the front controller and
built assets are reachable from the browser.

## Requirements

- Node 18+ and PHP 8.1+ (uses named args / `JSON_THROW_ON_ERROR`).

## Development (two processes)

```bash
npm install

# Terminal 1 — Vite dev server (HMR + TS compilation)
npm run dev

# Terminal 2 — PHP, in development mode so it points at the Vite server
APP_ENV=development php -S localhost:8000 -t public public/index.php
```

Open http://localhost:8000. Edit `src/main.ts` or `src/style.css` and the page
hot-updates. Edit a `.php` view/controller and just refresh.

> The Vite dev server's default CORS policy trusts any `localhost` origin, so
> serving PHP on `localhost:8000` works with no extra config. If you serve PHP
> from a different host (Docker, `*.test`, an IP), set `server.cors.origin` in
> `vite.config.ts` — see the note in that file.

## Production

```bash
npm run build   # tsc typecheck + emit public/dist/ + manifest

# Serve public/ with any PHP server (no APP_ENV -> manifest mode)
php -S localhost:8000 -t public public/index.php
```

Point your real web server's document root at `public/`. Apache uses the included
`.htaccess`. For nginx:

```nginx
root /path/to/public;
location / { try_files $uri /index.php$is_args$args; }
location ~ \.php$ { include fastcgi_params; fastcgi_pass 127.0.0.1:9000;
                    fastcgi_param SCRIPT_FILENAME $document_root/index.php; }
```

## Add a page

1. `app/Controllers/ContactController.php` extending `Controller`, calling
   `$this->render('contact', [...])`.
2. `app/Views/contact.php` with the body.
3. One line in `app/bootstrap.php`:
   `$router->get('/contact', [ContactController::class, 'index']);`

All pages share the same bundled `main.ts`. If you want per-page bundles, add more
entries to `build.rollupOptions.input` and call `vite('src/other.ts')` in that view.
