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
- `npm test` – Tests login, admin bulk-deletion, and network-site-selection redirects in both source and minified assets. Rebuild the assets after source changes before running these tests.

Login redirects accept HTTP(S) URLs on the current origin and root-relative paths. The validator returns an absolute URL, preserving query strings and fragments without allowing a path beginning with `//` to become an external destination. Invalid redirects retain the existing page-reload fallback.

Admin bulk-deletion and network-site-selection redirects use the same validation rules. Invalid admin redirects do not navigate, and rejected site selections do not activate the loading state.

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

## English translations

The source strings are predominantly German. Both `en_US` and `en_GB` therefore need complete PO catalogs and compiled MO files; an empty English translation falls back to the German source.

Use WP-CLI's i18n commands (no running database required) to refresh the template and update only the English catalogs:

```bash
wp i18n make-pot . languages/membership2.pot --domain=membership2 --include=membership2.php,app,premium --package-name='PS Mitgliedschaften'
wp i18n update-po languages/membership2.pot languages/membership2-en_US.po
wp i18n update-po languages/membership2.pot languages/membership2-en_GB.po
```

Review new, changed and `fuzzy` entries before compiling. Remove `fuzzy` only after checking the translation, including both plural forms. Preserve printf placeholders, HTML attributes, URLs and shortcode tokens. British spelling belongs in visible prose, not technical identifiers or gateway names.

```bash
wp i18n make-mo languages/membership2-en_US.po
wp i18n make-mo languages/membership2-en_GB.po
php scripts/check-translations.php
```

The checker uses the WordPress/ClassicPress POMO classes from the enclosing installation, without bootstrapping WordPress or accessing its database. For a standalone checkout, pass an installation path: `php scripts/check-translations.php /path/to/wordpress`. It checks template coverage, locale and plural headers, empty/fuzzy entries, placeholder and markup preservation, exact PO/MO agreement, and runtime singular/plural lookups.
