---
id: D-KNW-011
date: 2026-08-02
status: open
---

# D-KNW-011 — A rule that names a defect names its correction

**A knowledge rule that calls something a defect also says how it is corrected,
because a caller holding the defect has to invent a correction otherwise.**

The language-files rule states the steady state — the source XLF is English, a
translation goes into the locale-prefixed file beside it — and adds that a
non-English source file is a defect to report. It never says what reporting it
recommends. A session auditing a sitepackage whose source files declare
`source-language="de"` therefore had two remedies open to it, and both read as
consistent with the rule.

## Evidence

- The rule is delivered and the correction is not. `typo3_hint_lookup`,
  run against this branch with the task "TYPO3 extension conformance audit: the
  sitepackage XLF files declare source-language=\"de\"; what is the correct
  source language convention and how is it corrected?", returns the Language
  Files hints in full. Nothing in that answer says what to do with the file.
- Step 1a is out. `bin/cli hints:probe` on the feedback's own query reaches
  `language-files`, and the rule landed in `8f0f589` on 2026-07-30 — a day
  before this feedback was filed. `R-KNW-033` holds it and a test names it.
- The reporting session had the rule. Its sibling feedback of the same audit,
  `2026-07-31-193109`, lists "Settings.definitions.yaml labels — all German, no
  English source" among what it found. What it got wrong is only the remedy.
- Its own finding offered both remedies at once: "Add en.xlf files with English
  source (or switch source to en and add de.xlf)". The second is the convention
  and the first is not, and the hint separates them nowhere.
- The TYPO3 claim holds, read in `.checkouts/14.3`. Every core source file under
  `Resources/Private/Language/` is unprefixed and declares
  `source-language="en"`. Locale-prefixed files exist for de, fr, da, ru and
  others; the only `en.`-prefixed XLF in the tree is a Fluid test fixture.
- Adding an `en.`-prefixed file does not correct the defect. `LabelFileResolver`
  reads the unprefixed file as the `default` locale, and `LocalizationFactory`
  sets `default` as the fallback of every locale. The German labels keep
  surfacing wherever a locale has no translation of its own.
- The correction has a shape to state. A translation file declares
  `source-language="en" target-language="de"`, and each unit carries the English
  `<source>` beside the German `<target>` under the id the source file already
  uses.
- `typo3_extension_describe` is not the gap. It reports the `source-language` each
  file declares and says in the same answer that the rule for what it should
  declare belongs to `typo3_hint_lookup`.

## Decided

- Step 4 of the ladder, and queued rather than closed on the spot. The rewrite
  states an authoring procedure, which is a statement about TYPO3 and has to
  hold on every branch this server covers.
- Not step 3. Routing is a second question and does not answer this one: no
  wording anywhere below `knowledge/` names the correction, so a hint that
  arrived earlier would have left the same two remedies open.
- Not step 5. Nothing was traded away here. The rule was written to stop new
  German labels being added and it does that; the package already in the wrong
  state is a case it was not written for.

## Assumed

- The session read the hint rather than answering from its own knowledge. That
  cannot be established from here, and the lever is the same either way: this
  repository can name the correction and does not.

## Wrong if

- The corrected wording lands and a later run still recommends adding an
  `en.`-prefixed file. Then the wording was not what held the reader back, and
  the gap is step 2 instead.
- The audit-shaped query is what fails.
  `bin/cli hints:probe "TYPO3 extension conformance audit of a site package"`
  reaches `sitepackage-layout` and not `language-files` today, so a session that
  never phrases its task around labels is not offered the rule at all. A second
  feedback of this shape, from a run that demonstrably called
  `typo3_hint_lookup` without naming a language file, would move the
  answer to step 2.

## Since then

The rewrite queued here landed in `0e6cf08` on 2026-08-02, and the first **Wrong
if** did not fire on the next run to meet it. `feedback/2026-08-03-164659` is a
conformance audit of `EXT:guidedtour` against a TYPO3 14.3.5 installation, filed
a day later. It quotes the corrected wording back nearly verbatim and recommends
an `en.`-prefixed file nowhere, so the correction arrived and was read.

The second **Wrong if** is untested rather than cleared: that session named
`Resources/Private/Language/locallang.xlf` in the `paths` of its
`typo3_task_guide` call, which is the case the rule already reaches.

What the audit reports instead is the other direction of the same rule. The
correction says what a translation file declares; it never says what a
translation file missing that declaration does, so nothing in it fires on a
`de.locallang.xlf` that is already wrong — which is the direction
`skills/typo3-extension-conformance` asks every returned rule to be read in.
That is a gap rather than a wording failure, because the fact it needs is a
runtime consequence that changed in v14, and it has an entry of its own in
[`D-KNW-050`](knw-050-what-a-missing-target-language-does-to-a-translation-file-is-a-gap-this-server-owns.md).
