# Work out how `typo3/tailor` is installed and set up for an extension

**Serves:** R-SKL-009
**Priority:** normal
**Waiting on:** whether extension release gets a skill at all, and whether it is
    the release run rather than the setup. Put to the maintainer on 2026-08-04
    with three answers priced — measure the run first, write the skill from the
    measured facts, or delete the card — and deliberately kept for later rather
    than left unread, so a session reaching this is not the first to look at it
    and re-deriving the question buys nothing. Nothing is broken while it waits:
    `extension-ter-release` answers a release question today and is what a
    caller reaches. What would move it is a filed session bringing the wording,
    or somebody wanting the release run driven end to end.

    The question as it stands: the card was written to produce a skill ordering
    Tailor setup, and the setup that was measured does not carry one — it is
    four facts, two of which other tooling already enforces, and the choice
    between an `.env` and CI secrets follows from where Tailor is installed
    rather than from any order of steps. What has steps and an order, and what
    the range named for this covers from tagging through changelog and version
    numbers to publishing, is the release run: `set-version`, commit, tag, push,
    `ter:publish`. Whether that earns a skill is what is open, and
    `writing-a-skill.md` settles a domain with a run rather than with a shape,
    which this has not got: the `E-EXT` run behind `R-SKL-009` established the
    gap and is not a run of the workflow. A run of it would stop short of
    `ter:publish`, which needs a TER token nothing here holds.

The hint this card owed is written. `extension-ter-release` in
`knowledge/hints/extension.json` carries the four measured requirements and is
what a release question reaches —
`bin/cli hints:probe "release the extension with tailor"` ranks it first and
alone. The existing `extension-manifest` sentence about Tailor and the TER
reading `ext_emconf.php` is untouched, because it answers what makes a directory
an extension and this answers what a release needs.

## What was measured, 2026-08-04

`typo3/tailor` 1.7.0, the newest release on Packagist, installed into a scratch
project and run against a fixture extension. It is not vendored in this
repository and nothing here requires it. `tailor --version` reports `1.6.0`
because the string is hardcoded in `bin/tailor`; `composer show` is what says
which version is in play.

**It is a CLI, and the release path its own documentation prescribes is CI.**
The README's installation section names one install —
`composer req --dev typo3/tailor` — while every CI example in the same README
installs it globally and throwaway at job time and calls `ter:publish`. The
TYPO3 documentation prescribes nothing itself: it names Tailor as the REST route
preferable for automated publishing and delegates to that README. So the dev
dependency is the only install that is a setup act on the extension, and the
release path the tool documents does not need it.

**Where `.env` is read depends on which install was chosen.** `bin/tailor` looks
for `.env` at three paths relative to itself — `../.env`, `../../../../.env`,
`../../../../../.env`. Installed as the extension's dev dependency, the second
is the extension root, and a key placed only there was picked up: the artefact
came out named from it. Installed globally, those paths are the Composer home
instead and the extension's `.env` is never read, which is why the CI examples
pass secrets as environment variables. The file holds the API token either way.

**Four things the extension itself has to carry**, and two are already forced by
other tooling:

- The extension key, resolved in that order from the argument,
  `TYPO3_EXTENSION_KEY`, and `extra.typo3/cms.extension-key`. Not a setup step
  in practice: `composer install` on a `typo3-cms-extension` package without it
  already fails in `ExtensionKeyResolver`.
- `ext_emconf.php`, with a `version` equal to the version argument and a
  `constraints.depends.typo3` entry. `create-artefact` against a mismatched
  version fails, against a missing file fails, and `set-version` fails without
  it too.
- `.gitignore` for `tailor-version-artefact/`. The zip is written into the
  working directory, and the fixture's own `git archive` shipped the archive it
  had just produced.
- Optionally `TYPO3_EXCLUDE_FROM_PACKAGING`, naming a replacement for
  `conf/ExcludeFromPackaging.php`.

The packaging findings are evidence for `R-SKL-009` and are recorded there.
