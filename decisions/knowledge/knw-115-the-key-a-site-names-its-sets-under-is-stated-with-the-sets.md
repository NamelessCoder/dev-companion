---
id: D-KNW-115
title: The key a site names its sets under is stated with the sets
date: 2026-08-24
status: open
coveredBy:
  - HintsTest::aComposerDependencyQuestionIsAnsweredByTheRepositoryHint
  - HintsTest::theKeyASiteNamesItsSetsUnderIsAnsweredBySiteSets
---

# D-KNW-115 — The key a site names its sets under is stated with the sets

**That a site's own `config.yaml` names its sets under `dependencies` is stated
in `fresh-instance-seeding`, a hint about packages that seed, and it belongs in
`site-sets`, where a site-configuration query lands.**

The reporting session names that key as the one thing it could not have guessed
and read the core constructor to find it. The corpus had the sentence and the
question does not reach it: what a site-configuration query returns is
`site-sets`, which names `dependencies` for the *set's* own `config.yaml` — the
other file of that name — so a reader who gets that far is one file off.

## Evidence

- Re-run on 2026-08-24. The feedback names no call, and says so, so what was
  re-run is `bin/cli hints:probe` on the question it would have asked: "What in
  config/sites/<id>/config.yaml makes Site::isTypoScriptRoot() return true, and
  which YAML key becomes Site::getSets()?" returns `initial-content-references`,
  `project-configuration-files` and `environment-placeholders`, none of which is
  about a site's `config.yaml`.
- The statement is here. The fifth of `fresh-instance-seeding` says a set
  "reaches an installation only where a site's own config.yaml names it under
  dependencies". It ranks fifth on the query above, behind
  `dependency-injection` on text alone, and
  `"site config.yaml dependencies key sets Site::getSets"` returns two hints of
  which it is neither.
- Its curated phrases are what keep it there: "fills a fresh instance", "nothing
  to look at after the install", "ext_tables_static+adt.sql", "setup wizard".
  None is a site-configuration phrase, so the sentence is reachable from the
  question about a package that fills an instance and from no other.
- `site-sets` is where a site-configuration query does land — with
  `site-set-settings`, on
  `"site sets config.yaml settings.yaml site configuration"` — and its first
  statement names `dependencies` as what a set's own `config.yaml` lists other
  sets under. One word, two files, and the corpus carries it for the one the
  caller was not asking about.
- The direction the session needed is in neither hint. A search of `knowledge/`
  for `dependencies` returns those two statements and nothing naming the site
  entity's `getSets()`, so no answer here crosses from the YAML key to the
  accessor.
- The claims about TYPO3 hold.
  `$this->sets = $configuration['dependencies'] ?? []` and `isTypoScriptRoot()`
  returning
  `$this->sets !== [] || $this->typoscript !== null || $this->tsConfig !== null`
  read the same on `.checkouts/13.4` and `.checkouts/main`; `.checkouts/12.4`
  has neither, so the statement is bound from 13. The constructor takes
  `?SiteSettings $settings = null, ?SiteTypoScript $typoscript = null, ?SiteTSconfig $tsConfig = null`
  and falls back to
  `SiteSettings::createFromSettingsTree($configuration['settings'] ?? [])`, as
  reported. `getSets()` is public and documented; `isTypoScriptRoot()` is
  `@internal` on both lines.
- The tool reads as what it is. `typo3_configuration_lookup` names
  `TYPO3_CONF_VARS` three times in its description and takes a path into it, so
  the session read the name right and skipped the tool right.
- There is no destination for the question. The `routing` block of
  `knowledge/server-scope.json` carries no entry for a site-configuration
  question, and the `doesNotCover` entry that names one — "Deciding one site's
  configuration" — hands the format to https://docs.typo3.org/ and is read
  through `typo3_server_scope`, a call this session did not make.

## Decided

- Step 2 of the ladder with a step 1a remainder, and queued rather than closed
  on the spot. A statement about TYPO3 is written, which
  [`judging.rst`](../../documentation/records/judging.rst) puts on the todo's
  side of the line whatever its size.
- The site's key is stated in `site-sets`, curated on the site's own file rather
  than on the set's. `fresh-instance-seeding` keeps its sentence — a package
  that ships sets alone seeds nothing, and the site's key is what makes that
  point — and whether it owes a pointer at the new statement is the todo's to
  decide against `D-KNW-087`.
- The key-to-accessor direction goes with it: `dependencies` reaches the site
  entity's `getSets()`, `settings` reaches its `getSettings()`, and the entity's
  own TypoScript and page TSconfig come from the files beside `config.yaml`
  rather than from a key in it. That is the half the corpus states in neither
  direction, and it is what makes a lexical search on "sets" reach the file that
  spells it "dependencies".
- `isTypoScriptRoot()` is not decided here. It is `@internal` on every covered
  line, and how far the corpus follows internal API is a reading the todo does
  rather than a judgement this run can make.
- The description clause the feedback asks for is refused in the form it asks
  for it. What the session lacked was somewhere to go, not a disclaimer in a
  tool it had already skipped, and a sentence naming site configuration inside a
  `TYPO3_CONF_VARS` tool is read by nobody who read the name and moved on.
- No new `routing` entry either. The `doesNotCover` entry already sends a
  site-configuration question to `typo3_hint_lookup` with `id=site-sets`, and
  the placement above is what makes that pointer land on the answer instead of
  one file away from it.
- `normal` rather than the `low` the card arrived at. One session, and it
  counted what the gap cost — four reads of the core, three of them widening the
  same window — which is the measure `D-FBK-027` sets.
- Not `high`. One session and one task shape, and what is missing is a placement
  and one direction rather than a subject nobody has written.
- Neither archived nor trimmed. Nothing the session asked for is reachable from
  a site-configuration query today.

## Assumed

- That the session would have found the statement had it been in `site-sets`. It
  called nothing, so what a probe would have returned to it is read off what the
  probes return now.
- That `dependencies` is not what it would have searched for. The feedback says
  it would have searched "sets" and got nothing useful, which is why the
  statement has to carry both words rather than the key alone.
- That the site's `dependencies` and the set's are worth two statements. They
  are one word in two files, and a single sentence covering both is what the
  corpus has today.

## Wrong if

- The statement lands in `site-sets` and a site-configuration query still
  returns `fresh-instance-seeding` first. The lever would have been that hint's
  own curation, and this is step 4.
- A session with a site-YAML question reads `typo3_configuration_lookup`'s
  description and files this report anyway. The clause was the lever and the
  refusal above is wrong.
- The reading finds the site key documented in the manual under wording a
  `typo3_documentation_lookup` query reaches. Then the `doesNotCover` entry's
  "take the configuration format from docs.typo3.org" was already the right
  answer, and what is missing is a route to it rather than a statement.
- The sets turn out to be read off a site by a resolver rather than by the
  entity the caller holds. The mapping would rest on an accessor read out of its
  call path.
- The next session needs the key while writing a core functional test, as this
  one did, and does not reach a hint about site sets from that task at all. What
  is missing would be in the core testing documents rather than in the corpus.

## Since then

The statement is in `site-sets`, with the direction the corpus stated in neither
half: a site names its sets under `dependencies` in its own `config.yaml`, that
list is what the site entity's `getSets()` returns, `settings` reaches
`getSettings()`, and the site's own TypoScript and page TSconfig come from
`setup.typoscript`, `constants.typoscript` and `page.tsconfig` beside the file
rather than from a key in it. The entity and `SiteConfiguration` read line for
line the same on `.checkouts/13.4` and `.checkouts/main`, and `.checkouts/12.4`
has neither the key nor the accessor, so the statements are bound from 13. The
fourth **Wrong if** did not happen: `SiteConfiguration` hands the raw
configuration to the constructor, which keeps the list, and `SetRegistry`
resolves only what those names contain.

`isTypoScriptRoot()` is stated, with its marker and with what it decides. The
corpus already states an `@internal` member where a caller needs one, which
`assets.json` does, and what this one buys is the consequence rather than the
method: a site naming a set or holding one of those files renders without a
`sys_template` record on its root page, and a site with neither errors out
instead of rendering. That holds on both covered lines from a different class
each — `TypoScriptFrontendInitialization` on 13, `PageInformationFactory` on
main — so the statement names the behaviour and not the message.

The placement alone did not make the question reachable, and the domain is what
did. A site's `config.yaml` is YAML, `Domains` reads YAML as PHP, and a hint
filed under TypoScript alone is no candidate for a PHP query unless the task
spells out a curated phrase no PHP hint claims — "site configuration" is claimed
by `routing-request-handling`. So `site-sets` is asked from `php` as well, as
fifteen hints already are, and the reporting session's own question returns it
first. What that risks is a PHP query answered with `site-sets` in place of the
hint that was about it: the bare word `dependencies`, curated onto the hint, put
it above `extension-repository-dependencies` on a `composer.json` question,
which is why the key is curated as "dependencies key" and "under dependencies"
instead.

`fresh-instance-seeding` keeps its sentence and owes no pointer. Its reader is
asking what fills a fresh instance, and `site-sets` says nothing about that, so
a pointer there would name no consequence for the reader who is holding that
question — which is what `D-KNW-087` asks of the pointers that exist rather than
a reason to write one.
