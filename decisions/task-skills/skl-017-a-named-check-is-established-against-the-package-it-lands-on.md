---
id: D-SKL-017
date: 2026-08-04
status: open
---

# D-SKL-017 — A named check is established against the package it lands on

**`static-quality.md` names a tool per check and says what has to be established
about it on the package in hand: that it resolves, and that it finds something
there.**

Three sessions of 2026-08-04 read the page as a list to install. Two installed a
tool that could not carry the check, one installed a tool that could not be
installed at all, and each of the three worked the answer out from the tool's
own source.

## Evidence

- `feedback/2026-08-04-175840`: `helmich/typo3-typoscript-lint` cannot be
  required on a TYPO3 14.3 project. v3.3.0 and dev-master both require
  `symfony/event-dispatcher ^5.4 || ^6.4 || ^7.0` and the installation carries
  8.1.2; the session verified dev-master with a `--dry-run` require. What the
  maintainer asked for afterwards was an isolated tool install — a second
  `composer.json` below `Build/` with its own `vendor/` — which nothing on the
  page carries.
- `feedback/2026-08-04-175856`: the "Shipped configuration and data" row asks
  for "XML well-formedness for the XLIFF files" and names no tool, while its
  three neighbours name one each. The session wrote well-formedness by hand with
  `DOMDocument`; `symfony/translation` was already installed and ships
  `XliffLintCommand`, which validates against the XSD.
- `feedback/2026-08-04-180154`: on the package in hand `eslint` with its
  recommended preset reported 0 findings across one script and eleven specs,
  while Biome reported 10 — one of them a `boundingBox()` read with no null
  guard between two tests that guarded. The eslint stack was ~78 of the 208
  installed npm packages. On the same package stylelint reported 58 and Biome 0,
  so the page's other name held.
- The page already applies this standard once, to a version matrix: "a matrix
  whose every cell runs only version-independent steps proves that the files
  parse and nothing more — say so". A linter reporting nothing on the only file
  it guards is the same claim about a different check.
- [writing-a-skill.md](../../documentation/contributing/writing-a-skill.md) is
  why the three measurements do not become sentences in the page: no dependency
  constraint and no API signature stands in a published skill, because no
  release of this server corrects the copy in somebody else's project.

## Decided

- The judgement is **step 4 of the ladder**, wording, for all three. Two of the
  three tools are named on the page and the third exists; what is missing is
  what a session establishes before it declares the row covered.
- **One todo carries the three rows.** Three cards against one page are three
  rewrites of it, and the sessions arrived at one gap from three directions.
- The instruction goes into the skill and the facts do not. What resolves
  against which Symfony major, what an XLIFF linter's constructor defaults to
  and what a preset finds are facts that move, and `knowledge/` is where a
  statement carries `since` and `until`.
- The isolated tool install is named as the option where a check tool's
  constraints cannot meet the project's, since that is the general answer rather
  than one about TypoScript.
- Whether Biome or oxlint is named is left to the todo. One measurement on one
  small package is evidence that the page has to ask, not evidence about which
  tool wins.

## Assumed

- That the three measurements hold. Nothing here reproduced them — this run read
  this repository and no installation — so reproducing them is the todo's first
  step rather than a formality.
- That a session which is told to measure does. The page's matrix sentence is
  the same instruction and the same three sessions followed it.

## Wrong if

- A session reads the rewritten page, measures, finds nothing, and drops a check
  the project needed. Then the instruction bought a gap rather than closed one,
  and the row has to say what a nothing-finding means.
- `helmich/typo3-typoscript-lint` releases against Symfony 8 and the isolated
  install reads as a detour. Then the page named a workaround for a window.
- A fourth session installs a named tool without resolving it after the sentence
  is there. Then the naming is what misleads, and the row drops the tool rather
  than qualifying it.

**Since then**, on 2026-08-04, the two measurements that decide the wording were
made and the third was dropped.

`helmich/typo3-typoscript-lint` v3.3.0 installs on TYPO3 14.3.5: a real require
against `.environments/e-site-14.3`, which carries `symfony/event-dispatcher`
v7.4.15. `typo3/cms-core` 14.3.5 requires that package nowhere — only
`event-dispatcher-contracts ^3.6.0` — and `composer why` in the reporting
project names `friendsofphp/php-cs-fixer` and `symfony/mailer` as what pulled
its 8.1.2. So the page says what a tool has to meet is what the project
resolved, and never that a tool fails on a TYPO3 version.

`XliffLintCommand` was run over a locale-prefixed `de.locallang.xlf` declaring
`target-language="de"`, from the same installation's `symfony/translation`. At
the default it exits 1 with "a mismatch between the language included in the
file name and the `de` value used in the `target-language` attribute"; with
`requireStrictFileNames` false it exits 0. That is in `language-files` rather
than in the page, with what the linter does not establish beside it.

Two of the feedback's own claims did not survive. Only `RepeatingRValue` advises
extracting a repeated value into a constant — `DuplicateAssignment` reports an
overwritten value and advises nothing — and what the page carries instead is
that a linter's shipped configuration is merged under the project's, which
`ConfigurationLocator.php:53` does, and that its advice is its own rather than
this project's. The corpus already contradicts the advice: `site-set-settings`
says settings replaced constants.

The frontend measurement was dropped rather than deferred. What the page gains
there is the instruction to run a named linter over the files it guards, and
that sentence is the same whichever tool wins on one small package — which this
entry had already decided against naming.
