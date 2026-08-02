# Discovery — which installation is read, and how

Everything the server reads from a working installation rests on finding it
first: the packages, the console, the version. A wrong answer here is invisible,
because it looks exactly like an installation that has nothing to say.

See [the decisions readme](../readme.md) for how an entry is written and when
one is added.

- [`D-DIS-006`][D-DIS-006] — The protocol offers nothing to replace the working directory · 2026-08-01
- [`D-DIS-005`][D-DIS-005] — A registry with no console command is read by booting the installation · 2026-07-31 · confirmed
- [`D-DIS-001`][D-DIS-001] — The root package counts as an installed package · 2026-07-29 · confirmed
- [`D-DIS-004`][D-DIS-004] — The version comes from the core package, not from the console · 2026-07-29 · confirmed

[D-DIS-006]: dis-006-the-protocol-offers-nothing-to-replace-the-working-directory.md
[D-DIS-005]: dis-005-a-registry-with-no-command-is-read-by-booting-the-installation.md
[D-DIS-001]: dis-001-the-root-package-counts-as-an-installed-package.md
[D-DIS-004]: dis-004-the-version-comes-from-the-core-package-not-from-the-console.md

### Revoked, and kept as the record

- [`D-DIS-002`][D-DIS-002] — Discovery honours the declared vendor-dir and bin-dir · 2026-07-29
- [`D-DIS-003`][D-DIS-003] — A label query is words, and the console is asked with a regex · 2026-07-29

[D-DIS-002]: dis-002-discovery-honours-the-declared-vendor-dir-and-bin-dir.md
[D-DIS-003]: dis-003-a-label-query-is-words-and-the-console-is-asked-with-a-regex.md
