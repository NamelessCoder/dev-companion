---
id: D-KNW-015
date: 2026-08-02
status: revoked
revokedBy: D-KNW-021
---

# D-KNW-015 — A Fluid preview template replaces the content half, and nothing here says so

**A Fluid preview template replaces the content half of a page module preview
and not the header above it, and nothing in this server says so.**

The manual this server serves does not say it either, which makes the statement
this server's own rather than a restatement, and the feedback is queued.

The corpus registers a preview template and stops at the TSconfig key. A session
that follows it writes a template against `{record}` with no way of knowing what
is already on the page above the template's own output, and the duplicate header
is the first thing an editor sees.

## Evidence

- The miss reproduces against the manual as this server serves it today. Called
  over stdio with the feedback's own `page` argument and `targetVersion:
  "14.3"`, `typo3_documentation_lookup` returns *Configure custom backend
  preview for content element* with `status: answered`, and the example under
  *Preview rendering with a Fluid template and page TSconfig* is still
  `<h2>{record.header}</h2>` followed by `<p>{record.bodytext}</p>`. Nothing in
  that section says the header is drawn by anybody else.
  `renderPageModulePreviewHeader()` does appear on the page — in the
  `PreviewRendererInterface` listing under *Writing a preview renderer*, which
  is the PHP route a session taking the TSconfig route has no reason to read.
- The feedback's claim about TYPO3 holds. On `.checkouts/14.3`,
  `GridColumnItem::getPreview()` calls `renderPageModulePreviewHeader()` first,
  dispatches `PageContentPreviewRenderingEvent` after it, and uses the
  listener's value only as the *content*; `wrapPageModulePreview()` then puts
  the header into `<div class="element-preview-header">` and the content into
  `<div class="element-preview-content">`. `FluidBasedContentPreviewRenderer` is
  exactly such a listener — it renders the template with `record` assigned and
  calls `$event->setPreviewContent()`, and touches nothing else.
- What the header already carries is four things rather than one, so the
  duplication the feedback reports is one case of a shape:
  `element-preview-header-status` (the hidden-header notice when `header_layout`
  is `100`), `-date`, `-header` (the schema's label field, linked to the edit
  form) and `-subheader`. A template that repeats `{record.subheader}` or
  `{record.date}` makes the same mistake as one that repeats `{record.header}`.
  `renderPageModulePreviewFooter()` is the same story below the content.
- The split is not version-bound, which is what decides whether the statement
  needs a `since`. On `.checkouts/13.4` `GridColumnItem::getPreview()` has
  `renderPageModulePreviewHeader()` at line 78, the event after it and
  `wrapPageModulePreview()` at line 96, and the 13.4 renderer builds the same
  four header parts. So one statement holds on both majors — unlike the one
  [`D-KNW-014`](knw-014-the-record-variable-a-v14-preview-template-is-handed-is-a-gap-this-server-owns.md)
  queues, which is `since 14`.
- Nothing below `knowledge/` or `skills/` says it. `bin/cli hints:probe` reaches
  nothing on "backend preview element header already rendered default renderer"
  or on "backend preview template header duplicate StandardContentPreviewRenderer".
  The one preview statement on the `content-elements` hint gives the TSconfig key
  `mod.web_layout.tt_content.preview.<CType>` and the TypoScript beside it;
  `skills/typo3-content-element-development/SKILL.md` has one line, "Add a useful
  backend preview for a custom CType".
- The same gap is reported from the other side by
  `feedback/2026-08-01-003935-guidance-item-previews-for-content-elements.md`,
  which asks for a preview that summarises the assigned data rather than "a
  re-render of fields the default renderer already shows". That feedback is
  unjudged and its own card is in the queue, so it is corroboration here rather
  than something this run decided.

## Decided

- Step 1a of the ladder, and queued. Content elements are inside this server's
  scope, the answer is in neither `knowledge/` nor `skills/`, and there is
  nothing here to reword or move.
- Not closed on the spot. What lands is a statement in the corpus about TYPO3
  behaviour, and the judging run's reading has to be redone against both majors
  by the run that writes it.
- The feedback's own suggestion is addressed to the wrong repository. "Annotate
  the preview-template example" is a change to docs.typo3.org; the lever this
  server has is a statement of its own, and it is a stronger one than an
  annotation would be, because the manual being silent is what makes the
  statement worth carrying. Recorded so the todo does not open by looking for a
  documentation fix.
- The statement lands on the same `content-elements` preview statement that
  `D-KNW-014`'s todo rewrites, so the two are ordered rather than parallel:
  whichever lands second rewrites in place.

## Assumed

- The statement belongs on `content-elements` beside the preview statement
  already there, rather than on `fluid-templates` — the assumption `D-KNW-014`
  makes, for the same reason: a session writing a preview template arrives from
  the registration.
- Naming the four header parts is worth more than one sentence saying the header
  is already drawn. A session that reads only the shorter form still writes
  `{record.subheader}`.

## Wrong if

- A listener is allowed to replace the whole preview rather than its content
  half — core moves the header behind the event, or `wrapPageModulePreview()`
  stops being called with a separately rendered header. Then a template written
  to the statement shows no header at all, which is worse than the duplicate.
- The header block turns out not to duplicate `{record.header}` for the tables a
  Fluid preview is registered for. It renders the schema's *label* capability,
  not a field named `header`, so the statement as phrased is too narrow if
  previews are registered for tables whose label is another field.
- The todo finds the answer already reachable through
  `typo3_documentation_lookup` on another page — the TSconfig reference or the
  page module chapter. It is then step 2 or 3 and the corpus wants a pointer
  rather than a statement of its own.

## Confirmed on 2026-08-02

The run that wrote the statement redid the reading on both checkouts, and the
split holds as described. `GridColumnItem::getPreview()` calls
`renderPageModulePreviewHeader()` before it dispatches
`PageContentPreviewRenderingEvent` on 13.4 and on 14.3 alike, uses the
listener's value as the content only, and hands both to
`wrapPageModulePreview()`, which is unchanged between the two.
`FluidBasedContentPreviewRenderer` calls `setPreviewContent()` and nothing else
on both.

The four header parts are the same four on both majors, built from different
sources: 14.3 reads the label field off the schema's `Label` capability and
passes date, label and subheader through `RecordFieldPreviewProcessor` —
`prepareFieldWithLabel()` for the date, `prepareText()` plus `linkToEditForm()`
for the other two — where 13.4 reads `ctrl.label` from `$GLOBALS['TCA']` and
uses its own `renderText()` and `linkEditContent()`. Neither difference is
visible to a template, so the statement carries no `since`.

The narrowness the second **Wrong if** names did not materialise for the table
in play: `tt_content` declares `label` as `header` on both majors. The statement
says "the record type's label field" and gives `header` as the `tt_content`
case, so a preview registered for another table is covered by the phrasing
rather than contradicted by it.

The footer is stated with the header, because it is drawn by the same renderer
and the same template cannot reach it: `renderPageModulePreviewFooter()` is
called from `getFooterInfo()`, outside the `element-preview` wrap, and carries
the start and end time, the frontend user group, the two spacing fields and the
internal description.

The probe this entry recorded the gap on still reached nothing once the
statement was in, because `content-elements` carried no preview vocabulary at
all — the subject was indexed under the registration, `mod.web_layout` and the
CType. `backend preview` was added to its `appliesTo`, which is what the two
symptom probes now reach it by, and no query in the sweep changed hint.

What was not re-run is the manual: the third **Wrong if** was checked by the
judging run on the same day, and the page is unchanged since.

## Revoked on 2026-08-02

By the work this entry queued. Its headline says nothing here says so, and the
statement it asked for is on `content-elements` — the split, the four header
parts by field, and the six footer fields. A reader of a listing has the
headline and the status and nothing else, so `confirmed` beside "nothing here
says so" reads as a claim about a gap that is closed.

The **Confirmed on** above stays as the reading: both majors were re-read, the
statement carries no `since` because nothing between them is visible to a
template, and `backend preview` had to be added to the hint's `appliesTo` before
the symptom reached it at all.

What holds from here is
[`D-KNW-021`](knw-021-a-fluid-preview-template-replaces-the-content-half-and-the-corpus-says-so.md),
and what must keep holding is `R-KNW-042`, which now rests on the successor. One
of this entry's three **Wrong if** survives into it — the core moving the header
behind the event — and it is a different failure there: a statement in the corpus
that has gone false, rather than a statement nobody had written yet.
