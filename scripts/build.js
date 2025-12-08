const path = require('path');
const { build } = require('esbuild');

const root = path.resolve(__dirname, '..');

const jsTargets = [
  { in: 'app/assets/js/ms-admin.js', out: 'app/assets/js/ms-admin.min.js' },
  { in: 'app/assets/js/ms-public.js', out: 'app/assets/js/ms-public.min.js' },
  { in: 'app/assets/js/ms-public-ajax.js', out: 'app/assets/js/ms-public-ajax.min.js' },
  { in: 'app/assets/js/ms-admin-pointers.js', out: 'app/assets/js/ms-admin-pointers.min.js' },
];

const cssTargets = [
  { in: 'app/assets/css/ms-admin.css', out: 'app/assets/css/ms-admin.min.css' },
  { in: 'app/assets/css/ms-public.css', out: 'app/assets/css/ms-public.min.css' },
];

const commonJsOptions = {
  bundle: false,
  minify: true,
  sourcemap: false,
  target: 'es2018',
  legalComments: 'none',
};

const commonCssOptions = {
  bundle: false,
  minify: true,
  sourcemap: false,
  legalComments: 'none',
  loader: { '.css': 'css' },
};

async function buildAll() {
  for (const target of jsTargets) {
    await build({
      entryPoints: [path.join(root, target.in)],
      outfile: path.join(root, target.out),
      ...commonJsOptions,
    });
  }

  for (const target of cssTargets) {
    await build({
      entryPoints: [path.join(root, target.in)],
      outfile: path.join(root, target.out),
      ...commonCssOptions,
    });
  }
}

buildAll().catch((err) => {
  console.error(err);
  process.exit(1);
});
