# Say which Composer keys install TYPO3 below an extension

**Serves:** feedback/2026-08-03-162759-setting-the-extension-s-own-composer-json-up-as.md
**Priority:** normal
**Branch:** todo/say-which-composer-keys-install-typo3-below-an-extension
**Claimed:** 2026-08-03

Step 1a of the ladder, on the evidence in
[`D-KNW-047`](../../decisions/knowledge/knw-047-what-installs-typo3-below-the-extension-being-developed-is-a-gap-this-server-owns.md):
the corpus names `extra.typo3/cms.web-dir` only to say which directory is
served, and nothing says which keys put an installation below the package being
developed. Establish the three statements against an installation rather than
from the report — a root `composer.json` of package type `typo3-cms-extension`
that requires `typo3/cms-core`, with `config.vendor-dir`, `config.bin-dir` and
`extra.typo3/cms.web-dir` moved below one directory and
`extra.typo3/cms.app-dir` set beside them. What `composer install` prints about
`app-dir`, and whether it depends on the installer major or on `web-dir` being
set, is the first; that `config/` and `var/` therefore land in the Composer root
and belong in `.gitignore` is what follows from it. The second is confirmed
already and needs the range rather than the reading: `typo3/cms-core` requires
`typo3/cms-cli` at `^3.1.3` on `.checkouts/14.3`, in `typo3/sysext/core/composer.json`
line 92, so the constraint belongs to the core and a root require of it fails to
resolve — read the same line on `.checkouts/12.4` and `13.4` for how far the
statement holds. The third is where a root-package extension is loaded from,
with `typo3conf/ext/` empty beside it and `_assets/<hash>/` carrying its public
resources; `public-assets` states the `_assets` half already, so what is left is
the placement. Then write it into `project-build-and-scripts` in
`knowledge/hints/project.json` or a hint of its own, bound to the installer
major where the readings differ, with a requirement for what has to keep
holding.
