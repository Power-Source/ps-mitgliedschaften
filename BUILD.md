# Asset Build

This plugin now ships a minimal build pipeline for the in-repo JS/CSS assets. We keep the existing file names that WordPress enqueues (e.g. `app/assets/js/ms-admin.min.js`).

## Prerequisites
- Node.js 18+

## Install
```bash
npm install
```

## Commands
- `npm run build` – Minifies JS/CSS into the existing target files.
- `npm run dev` – Watches JS/CSS and writes non-minified output with sourcemaps for easier debugging.

## Release/Distribution (ohne Dev-Kram)
Aus dem Plugin-Root ein schlankes Zip bauen, das Doku/Build-Tooling auslässt:

```bash
zip -r ../ps-mitgliedschaften-prod.zip . \
	-x "docs/*" "scripts/*" "node_modules/*" \
		 "BUILD.md" "package.json" "package-lock.json" "pnpm-lock.yaml" \
		 ".git*" "*.log"
```

Das erzeugt `../ps-mitgliedschaften-prod.zip` ohne Docs und Build-Dependencies. Falls weitere Dev-Dateien existieren (z.B. `.vscode/`, `.editorconfig`), einfach weitere `-x` Patterns ergänzen. Bei Bedarf als Skript in `package.json` oder `scripts/` ablegen.

## Entrypoints
JS
- `app/assets/js/ms-admin.js`
- `app/assets/js/ms-public.js`
- `app/assets/js/ms-public-ajax.js`
- `app/assets/js/ms-admin-pointers.js`

CSS
- `app/assets/css/ms-admin.css`
- `app/assets/css/ms-public.css`

Outputs
- Minified files are written alongside sources as `*.min.js` / `*.min.css` in the same directories.

## Vendor files
Files like `app/assets/js/jquery.m2.plugins.min.js` remain vendored and are **not** rebuilt. If we later add their sources, include them as new entrypoints in `scripts/build.js`.
