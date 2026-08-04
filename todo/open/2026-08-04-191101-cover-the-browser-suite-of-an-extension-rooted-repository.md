# Cover the browser suite of an extension-rooted repository

**Serves:** knowledge/documents/, knowledge/hints/
**Priority:** normal

`project/testing/playwright` answers for a repository that is a site, and the
other case is a repository that is one extension with TYPO3 installed beneath
it. There the runtime is `.build/` — `config.vendor-dir`, `config.bin-dir` and
`extra.typo3/cms.web-dir` below it, which `extension-repository-installation`
already carries — and `Build/` holds the build scripts rather than the
installation. Two things in the shipped configuration are wrong there: the
report and results go to `../var/`, which at an extension root is inside the
generated `.build/` or does not exist, and the base URL is the site served from
`.build/public` rather than the repository's own. The three spec files are
unchanged. Settle first whether this is a second document under
`extension/testing/` or a section in the project one — the scope of a document
is its directory since `D-KNW-058`, and copying three files to change one path
is what a second document would cost. Then verify it by running, the way the
project one was: `.environments/` has no extension-rooted checkout today, so
that environment is part of the work.
