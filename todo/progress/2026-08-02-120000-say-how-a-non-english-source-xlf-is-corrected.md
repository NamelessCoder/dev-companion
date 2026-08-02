# Say how a non-English source XLF is corrected

**Serves:** feedback/2026-07-31-193619-5-recommended-add-en-xlf-files-with-english.md
**Priority:** normal
**Branch:** todo/say-how-a-non-english-source-xlf-is-corrected
**Claimed:** 2026-08-02

Step 4, and `D-KNW-011` carries the evidence: the `language-files` hint calls a
non-English source file a defect to report and never says what the report should
recommend, so an audit offered "add en.xlf" and "switch the source to en and add
de.xlf" as equal options. Extend the hint in
`knowledge/architecture-hints/general.json` and the label checklist in
`knowledge/task-intents.json` with the correction, having read the unit shape on
12.4, 13.4 and 14.3 under `.checkouts/` first: the source file keeps its path and
its unit ids and its wording becomes English, the existing wording moves into the
locale-prefixed file beside it as `<target>` under `source-language="en"
target-language="<locale>"`, and an `en.`-prefixed file is never the answer —
`LabelFileResolver` reads the unprefixed file as the `default` locale that every
other locale falls back to. Then widen `R-KNW-033` to the correction and the
assertion in `HintsTest::aNewLabelNamesTheSourceLanguageAndWhereItsTranslationGoes`
that holds it, and archive the feedback with `bin/cli feedback:archive`.
