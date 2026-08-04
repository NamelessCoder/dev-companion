# Cover the browser suite of an extension-rooted repository

**Serves:** knowledge/documents/, src/Upkeep/
**Priority:** normal

Measured on 2026-08-04 against a real extension-rooted installation — a DDEV
project whose docroot is `.build/public`, TYPO3 14.3 installed beneath the
extension's own `composer.json` with `config.vendor-dir`, `config.bin-dir` and
`extra.typo3/cms.web-dir` below `.build/`. Two things this card first claimed
are wrong. `config/` and `var/` sit at the extension root rather than inside
`.build/`, because `app-dir` is forced to the Composer root, so the artifact
paths in the shipped configuration hold unchanged and the suite wrote into
`var/playwright-results/` from there. And DDEV writes no `additional.php` for
this layout at all, so the database block that broke every E-SITE does not
appear. What is left of the difference is the site being served from
`.build/public` and the console being at `.build/bin/typo3`, and neither reaches
a Playwright file. So this is a paragraph in `project/testing/playwright` naming
the layout, not a second document — the three files it would copy are identical.
What is still missing is the environment: `bin/cli environment:create` makes no
extension-rooted installation, so the paragraph is verified against a scratch
project rather than against something this repository can make again, and that
scratch installation served 500 for lack of a page rather than for anything
about the layout.
