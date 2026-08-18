# State the ways a package fills a fresh instance, and that many declare none

**Serves:** feedback/2026-08-18-071500-nothing-answers-how-a-freshly-booted-extension.md
**Priority:** normal

Judged as step 1a and taken on: `D-KNW-096` has the evidence, the boundary and
what would show it wrong. Establish against `.checkouts/` which ways core lets a
package declare that it fills a fresh instance. Three are named already: the
`Initialisation/` data file with the site configuration beside it, a console
command, and a site set a site has to depend on. Write what holds into
`knowledge/hints/distribution.json` beside `sitepackage-initial-content`,
including that a package declaring none of them fills the instance by a
procedure only its own manual writes down. Then name that hint where the
question is asked: step 4 of `skills/typo3-development-installation/SKILL.md`
and the `installation-operations` checklist in `knowledge/task-intents.json`,
beside `typo3_extension_describe`, which already reports the two data files, the
`console.command` tag, the site sets and where the manual is. The answer may not
say which backend module is the wizard — that is outside the boundary the
decision sets.
