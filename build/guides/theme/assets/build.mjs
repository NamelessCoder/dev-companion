// The stylesheet and the script every rendered page links, built into `dist/`.
//
// Both carry a hash of their own content in the name, and `dist/manifest.txt`
// says what those names are. The layout reads that file at render time with
// Twig's `source()`, so a changed asset is a changed URL and no reader is
// served yesterday's stylesheet with today's markup — the cache buster that
// `D-DOC-017` counted as a moving part turns out to be two lines.
//
// `dist/` is a build product and is gitignored. `bin/cli documentation:assets`
// says so when it is missing, with the command that writes it.
import { build } from 'esbuild';
import { createHash } from 'node:crypto';
import { mkdir, readdir, readFile, rm, writeFile } from 'node:fs/promises';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname(fileURLToPath(import.meta.url));
const dist = join(here, 'dist');

const named = (name, contents) =>
    name.replace('.', '.' + createHash('sha256').update(contents).digest('hex').slice(0, 8) + '.');

// Nothing sweeps `dist/`, so a rebuild would otherwise leave every hash it has
// ever written standing beside the current one.
await rm(dist, { recursive: true, force: true });
await mkdir(dist, { recursive: true });

const script = await build({
    entryPoints: [join(here, 'site.js')],
    bundle: true,
    minify: true,
    format: 'iife',
    target: 'es2017',
    write: false,
});

const written = [];
for (const [name, contents] of [
    ['site.css', await readFile(join(here, 'site.css'))],
    ['site.js', Buffer.from(script.outputFiles[0].contents)],
]) {
    const hashed = named(name, contents);
    await writeFile(join(dist, hashed), contents);
    written.push(hashed);
}

await writeFile(join(dist, 'manifest.txt'), written.join('\n') + '\n');

for (const file of await readdir(dist)) {
    const size = (await readFile(join(dist, file))).length;
    console.log(`${file} — ${(size / 1024).toFixed(1)} KB`);
}
