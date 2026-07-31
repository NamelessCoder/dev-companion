# Discovery — which installation is read, and how

Everything the server reads from a working installation rests on finding it
first: the packages, the console, the version. A wrong answer here is invisible,
because it looks exactly like an installation that has nothing to say.

See [the decisions readme](../readme.md) for how an entry is written and when
one is added.

| Decided | Id | What was decided | State |
| --- | --- | --- | --- |
| 2026-07-29 | [`D-DIS-1`](dis-1-the-root-package-counts-as-an-installed-package.md) | The root package counts as an installed package | standing |
| 2026-07-29 | [`D-DIS-2`](dis-2-discovery-honours-the-declared-vendor-dir-and-bin-dir.md) | Discovery honours the declared vendor-dir and bin-dir | standing |
| 2026-07-29 | [`D-DIS-3`](dis-3-a-label-query-is-words-and-the-console-is-asked-with-a-regex.md) | A label query is words, and the console is asked with a regex | standing |
| 2026-07-29 | [`D-DIS-4`](dis-4-the-version-comes-from-the-core-package-not-from-the-console.md) | The version comes from the core package, not from the console | standing |
