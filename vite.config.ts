import { defineConfig } from 'vite'
import { resolve } from 'node:path'

// Vite runs as a pure asset pipeline here — PHP owns the HTML.
// In dev we hand the browser URLs pointing at the Vite dev server (HMR + on-the-fly
// TS compile); in build we emit hashed files + a manifest that PHP reads.
export default defineConfig(({ command }) => ({
  // Prod assets live under /dist/ (see build.outDir). In dev, serve from root so
  // the URLs PHP injects stay simple. This prefix is also baked into any
  // asset URLs inside the built JS/CSS, so it must match where files are served.
  base: command === 'build' ? '/dist/' : '/',

  // PHP owns public/, and our build output lives inside it (public/dist). Disable
  // Vite's static-copy publicDir so it doesn't try to copy public/ into itself.
  // Put any static passthrough files (favicon, robots.txt) directly in public/.
  publicDir: false,

  build: {
    // Emit into the PHP web root so the browser can fetch the built files.
    outDir: resolve(__dirname, 'public/dist'),
    emptyOutDir: true,
    // Writes public/dist/.vite/manifest.json — the source-path -> hashed-file map.
    manifest: true,
    rollupOptions: {
      // The one entry point. Its manifest key will be "src/main.ts".
      input: resolve(__dirname, 'src/main.ts'),
    },
  },

  server: {
    // Pin the port so the URL PHP injects in dev is deterministic. Without
    // strictPort, a busy 5173 would silently become 5174 and break the tags.
    host: 'localhost',
    port: 5173,
    strictPort: true,
    // Make Vite emit ABSOLUTE asset URLs (e.g. imported images) pointing back at
    // the dev server rather than at the PHP origin.
    origin: 'http://localhost:5173',
    // NOTE: the dev server's default CORS policy already trusts any localhost
    // origin, so serving PHP on http://localhost:8000 works out of the box.
    // If you serve PHP from a non-localhost host (Docker, *.test, an IP, etc.),
    // add it explicitly, e.g.:  cors: { origin: 'http://myapp.test' }
  },
}))
