// The stylesheet, the script and the two families every rendered page links,
// built into `dist/`.
//
// The stylesheet and the script carry a hash of their own content in the name,
// and `dist/manifest.txt` says what those names are. The layout reads that file
// at render time with Twig's `source()`, so a changed asset is a changed URL and
// no reader is served yesterday's stylesheet with today's markup — the cache
// buster that `D-DOC-017` counted as a moving part turns out to be two lines.
//
// The faces are not hashed: a weight of a subset of a family is what its name
// already says, and `@fontsource` publishes a new file rather than a new
// version of one. They land beside the pages flat, because `Site::publishAssets`
// copies by file name and the stylesheet asks for them the same way.
//
// `dist/` is a build product and is gitignored. `bin/cli documentation:render`
// stops when it is missing rather than publishing a site served unstyled.
import { build, transform } from 'esbuild';
import { createHash } from 'node:crypto';
import { mkdir, readdir, readFile, rm, writeFile } from 'node:fs/promises';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname(fileURLToPath(import.meta.url));
const dist = join(here, 'dist');
const packages = join(here, '..', '..', 'node_modules');

// The two families the design system allows, in the weights this site sets:
// 300 for the product half of the wordmark, 600 for what a heading and a
// control are set in, 400 and its italic for everything a person wrote.
const FAMILIES = [
    ['@fontsource/source-sans-3', ['300', '400', '400-italic', '600']],
    ['@fontsource/source-code-pro', ['400', '600']],
];

// Everything this corpus is written in. The other eleven subsets @fontsource
// ships are Cyrillic, Greek and Vietnamese, which no page here has a character
// of — and a face with no `unicode-range` would be downloaded for all of them.
const SUBSETS = /-(?:latin|latin-ext)-\d+-(?:normal|italic)\.woff2$/;

/** The @font-face rules, and the files they name. */
async function faces() {
    const rules = [];
    const files = new Map();

    for (const [family, weights] of FAMILIES) {
        for (const weight of weights) {
            const sheet = await readFile(join(packages, family, `${weight}.css`), 'utf8');
            for (const block of sheet.split('@font-face').slice(1)) {
                const rule = block.slice(0, block.indexOf('}') + 1);
                const named = rule.match(/url\(\.\/files\/([^)]+\.woff2)\)/);
                if (!named || !SUBSETS.test(named[1])) {
                    continue;
                }
                files.set(named[1], join(packages, family, 'files', named[1]));
                // The woff alongside it is dropped: every browser that reads
                // this site reads woff2, and keeping it doubles what is
                // published for a fallback nobody reaches.
                //
                // `optional` rather than @fontsource's `swap`: swapping means
                // every document lays the wordmark out in the fallback and
                // again when the face lands, which is a jump on each
                // navigation. Optional forbids the swap after the paint — the
                // preload in the layout is what gets the face there in time,
                // and a first visit that misses it reads in the fallback
                // rather than jumping.
                rules.push('@font-face' + rule
                    .replace(/src:[^;]+;/, `src: url(${named[1]}) format('woff2');`)
                    .replace('font-display: swap;', 'font-display: optional;'));
            }
        }
    }

    return { css: rules.join('\n'), files };
}

// Nothing sweeps `dist/`, so a rebuild would otherwise leave every hash it has
// ever written standing beside the current one.
await rm(dist, { recursive: true, force: true });
await mkdir(dist, { recursive: true });

const font = await faces();
for (const [name, from] of font.files) {
    await writeFile(join(dist, name), await readFile(from));
}

// The favicon is the only drawing a page links rather than inlines, because a
// browser tab is not a place `currentColor` reaches.
await writeFile(join(dist, 'signet-s.svg'), await readFile(join(here, 'icons', 'signet-s.svg')));

// What every `<sds-icon>` on this site resolves against — the ones the layout
// writes and the ones the components render inside themselves. Copied whole
// rather than subset, because which glyph a component reaches for is its
// business and not this build's: a subset goes blank the day one changes. It
// is 51 KB over the wire, fetched once and held for all 48 pages.
await writeFile(
    join(dist, 'actions.svg'),
    await readFile(join(packages, '@typo3', 'soul-design-system', 'dist', 'assets', 'icons', 'sprites', 'actions.svg')),
);

// The tokens are @imported by site.css and inlined here, so they arrive as one
// request with the rest.
const styles = await build({
    entryPoints: [join(here, 'site.css')],
    bundle: true,
    minify: true,
    write: false,
});

const script = await build({
    entryPoints: [join(here, 'site.js')],
    bundle: true,
    minify: true,
    format: 'iife',
    target: 'es2017',
    write: false,
});

const css = await transform(font.css + Buffer.from(styles.outputFiles[0].contents), { loader: 'css', minify: true });

const named = (name, contents) =>
    name.replace('.', '.' + createHash('sha256').update(contents).digest('hex').slice(0, 8) + '.');

const written = [];
for (const [name, contents] of [
    ['site.css', Buffer.from(css.code)],
    ['site.js', Buffer.from(script.outputFiles[0].contents)],
]) {
    const hashed = named(name, contents);
    await writeFile(join(dist, hashed), contents);
    written.push(hashed);
}

await writeFile(join(dist, 'manifest.txt'), written.join('\n') + '\n');

let total = 0;
for (const file of (await readdir(dist)).sort()) {
    const size = (await readFile(join(dist, file))).length;
    total += size;
    console.log(`${file} — ${(size / 1024).toFixed(1)} KB`);
}
console.log(`${(await readdir(dist)).length} files — ${(total / 1024).toFixed(1)} KB`);
