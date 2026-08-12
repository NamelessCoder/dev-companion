---
id: D-ANS-014
date: 2026-08-02
status: open
---

# D-ANS-014 — The extension answer enumerates registrations, not files — and a registration is one wherever it is declared

**`typo3_extension_scope` lists what an extension registers and never the files
it ships, and a registration belongs in that list wherever it is declared.**

A conformance audit asks for a ships section that names test files by path,
FlexForms, form definitions, route enhancers and the Configuration
subdirectories. Half of that is a file tree, which `glob` answers and the skill
already says to answer that way. The other half is three registration kinds this
answer omits while listing fifteen others, and their omission is not a boundary
anybody chose.

## Evidence

- `feedback/2026-07-31-194510`, re-run on 2026-08-02 through
  `bin/typo3-dev-companion` from this worktree, standing in
  `/home/benji/projects/site-new` — the directory it was written in, whose
  `opencode.json` and `.mcp.json` both name this repository's entrypoint.
  `typo3_extension_scope` with `printworks_sitepackage` answers from the booted
  installation.
- The XLF half is in that answer: "Ships: manual none, readme none, tests
  Functional+Unit", then `Resources/Private/Language/backend_fields.xlf`,
  `backend_layouts.xlf` and `messages.xlf`, each with "source-language de, no
  translations beside it". `artifacts` landed in `fc80db8` (2026-07-31 02:08
  +0200), an ancestor of `420b0ac`, which is what `main` stood at when the
  report was written nineteen and a half hours later at 21:45 local.
- `feedback/2026-07-31-194825` is the same call three minutes later from the
  same directory, by a different model, reporting that section as the thing that
  let it tell "missing" from "not yet read". The two are the same property read
  from both sides, which is the pairing
  [judging.md](../../documentation/records/judging.rst) names, and
  [`D-FBK-018`](../feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md)
  is why the strength is read as the boundary rather than as a confirmation.
- The file-listing half is somebody else's step, stated.
  `skills/typo3-extension-conformance/SKILL.md` says a surface is in scope
  because the checklist names it and not because the file tree shows it, that
  listing the files first inverts that, and to derive the list from the
  checklist and `typo3_extension_scope` and then let reading answer it. "I had
  to use glob and read to discover the full file tree" is that reading.
  `Extension::artifacts()` states the same line from this side: everything above
  it is what a caller can find more of by reading further, and the four below it
  are the ones whose absence has no file to stumble over.
- Three of the six asked for are not that. Each is a registration, each is
  declared in a file that stands still, and none is in the answer:
  - `Configuration/FlexForms/Catalogue.xml` and `Teaser.xml`, bound by
    `addPiFlexFormValue()` with a `FILE:EXT:` argument in
    `Configuration/TCA/Overrides/tt_content.php` — a file
    `Extension::overrides()` already tokenises, and a method already named in
    the comment above `Extension::TABLE_FIRST_METHODS`, where it is excluded
    from the *table* list for the correct reason that its first argument is not
    a table. Nothing else picks it up, so the binding leaves the answer
    entirely.
  - `Configuration/Sets/Printworks/route-enhancers.yaml`. Core reads that name
    exactly, in
    `.checkouts/14.3/typo3/sysext/core/Classes/Site/Set/YamlSetDefinitionProvider.php:123`,
    beside `settings.definitions.yaml`, `settings.yaml` and `labels.xlf`. The
    answer names the set as `bk2k/printworks (Configuration/Sets/Printworks/)`
    and says nothing about what the directory carries.
  - `Configuration/Form/Printworks/config.yaml`, which registers the form
    storage the extension's `Resources/Private/Forms/ProductRequest.form.yaml`
    lives in.
- The sharpest instance is inside the answer already. The two content elements
  it reports as "no templateName in this extension's TypoScript" —
  `printworkssitepackage_catalogue` and `printworkssitepackage_teaser` — are
  exactly the two carrying a FlexForm. The answer is least informative at the
  two entries whose unread file is what describes them.

## Decided

- The feedback is trimmed rather than closed. The XLF half is answered and goes;
  the three registration kinds stay open and a todo serves them, which is what
  [`D-FBK-017`](../feedback/fbk-017-a-judgement-turns-a-feedback-into-work-and-the-work-closes-it.md)
  requires of a judgement that does not archive.
- The file-enumeration half is declined, and the reason is recorded here rather
  than in the feedback, because it is a boundary and not a gap. Test files by
  path and "Configuration subdirectories not already covered" are a tree, a tree
  is what `glob` answers, and this answer's job is the part a tree walk cannot
  give: what the files mean, and which of them are not there.
- The three kinds are step 1b of the ladder — the shape is missing, the answer
  is available from files nobody can get it out of — and they touch the tool's
  declared schema, so they are queued rather than closed on the spot.
  [`R-ANS-014`](../../requirements/answers/ans-014-a-registration-is-answered-wherever-it-is-declared.md)
  records what must hold.
- What each of the three actually is in TYPO3 is left to that todo. Only
  `route-enhancers.yaml` was read in core here; the form-set mechanism and the
  general shape of a FlexForm binding were not, and naming a fix from this
  position is the copy-down [judging.md](../../documentation/records/judging.rst)
  warns about.

## Assumed

- That the reporting session called the server this checkout builds. Both client
  entries in that project name it, and `fc80db8` predates the report; what that
  working tree held at 21:45 is recorded nowhere.
- That one sitepackage is enough to show the gap. Three kinds were found in one
  extension, and whether the list is three or ten is the todo's first step
  rather than something this run established.
- That a registration kind is worth an entry even where another lookup reaches
  it from the other side. `typo3_configuration_lookup` answers effective
  configuration, which is not the same question as what this extension declares.

## Wrong if

- The three turn out not to stand still in the general case — a FlexForm bound
  through a path the file assembles, a form set registered by running. The line
  above would then select nothing reliable, and
  [`R-ANS-012`](../../requirements/answers/ans-012-an-answer-that-cannot-read-something-says-so.md)
  saying so is the whole of what can be added.
- A session holding an answer that carries all three still walks the tree with
  `glob` for the same surfaces. The declined half would then be the one that was
  wanted, and declining it here the error.
- A feedback reports the enumeration as noise — an extension whose answer is
  longer than the tree it describes. The boundary would then be in the right
  place and drawn too far out.

## Since then

`feedback/2026-07-31-194825`, the strength this entry reads as the other side of
the same property, was judged on 2026-08-02 and is not the confirmation it looks
like. The four artifacts are the exception this entry names — the ones whose
absence has no file to stumble over — and the exception holds for three of them.
`ExtensionScope` renders `manual`, `readme` and `tests` present or absent in one
`Ships:` line and renders the language files only where there are some, so an
extension shipping no XLF is answered with `languageFiles: []` in the data and
nothing at all in the text. `R-PRJ-006` requires the fourth as much as the other
three, and the test holding it covers the absent case in the data alone.

That is inside the artifacts section rather than at the boundary this entry
draws, so nothing decided here moves. The readings and the ladder step are on
[`D-FBK-018`](../feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md),
and the feedback is trimmed to that half and stays open behind the todo *Say the
missing translation the way the missing manual is said*.
