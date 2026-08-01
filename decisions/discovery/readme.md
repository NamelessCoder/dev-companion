# Discovery — which installation is read, and how

Everything the server reads from a working installation rests on finding it
first: the packages, the console, the version. A wrong answer here is invisible,
because it looks exactly like an installation that has nothing to say.

See [the decisions readme](../readme.md) for how an entry is written and when
one is added.

- [`D-DIS-6`][D-DIS-6] — The protocol offers nothing to replace the working directory · 2026-08-01 · standing
- [`D-DIS-5`][D-DIS-5] — A registry with no console command is read by booting the installation · 2026-07-31 · tested
- [`D-DIS-1`][D-DIS-1] — The root package counts as an installed package · 2026-07-29 · tested
- [`D-DIS-2`][D-DIS-2] — Discovery honours the declared vendor-dir and bin-dir · 2026-07-29 · corrected
- [`D-DIS-3`][D-DIS-3] — A label query is words, and the console is asked with a regex · 2026-07-29 · corrected
- [`D-DIS-4`][D-DIS-4] — The version comes from the core package, not from the console · 2026-07-29 · standing

[D-DIS-6]: dis-6-the-protocol-offers-nothing-to-replace-the-working-directory.md
[D-DIS-5]: dis-5-a-registry-with-no-command-is-read-by-booting-the-installation.md
[D-DIS-1]: dis-1-the-root-package-counts-as-an-installed-package.md
[D-DIS-2]: dis-2-discovery-honours-the-declared-vendor-dir-and-bin-dir.md
[D-DIS-3]: dis-3-a-label-query-is-words-and-the-console-is-asked-with-a-regex.md
[D-DIS-4]: dis-4-the-version-comes-from-the-core-package-not-from-the-console.md
