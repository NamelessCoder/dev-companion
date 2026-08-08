---
id: D-SKL-031
date: 2026-08-09
status: open
---

# D-SKL-031 — A triage picks a candidate on where the symptom is visible and what the suite already models

**A triage picks a candidate on where the symptom becomes visible and on how
much of the constellation the suite already models.** The triage skill says so
where it hands the backlog over.

[`D-ANS-069`](../answers/ans-069-a-backlog-row-carries-the-review-server-and-not-the-journal.md)
widened the row and left the criterion open, because reading a row is this
repository's question and what makes core work cheap is not.

## Evidence

- The reporting session's own set, re-read on 2026-08-09. The issue it settled,
  #58705, is a TypoScript reproduction, it stands `Under Review` with change
  95182 pushed on 2026-08-08, and `.checkouts/main` already carries
  `frontend/Tests/Functional/Imaging/GifBuilderTest.php` and
  `frontend/Tests/Functional/ContentObject/FilesContentObjectTest.php` over the
  two objects the report names.
- The four it decided cheaply divide by where the symptom is. Issue #82228
  carries an abandoned change on the review server, #83913 and #81102 are
  backend interactions — no `Tests` directory in `.checkouts/main` names the
  update signal #81102 is about — and #83848 carries no reproduction at all.
- A test over the class is not the signal on its own. Issue #85456 has
  `backend/Tests/Unit/Form/FormDataProvider/TcaColumnsOverridesTest.php`, all
  three providers it names exist on `main`, and it is still not settleable: the
  mechanism is the order between them, which a provider tested alone cannot see.
  The reading is the level the symptom appears at, not the file.
- The category reading is free and rare. Of the 55 categories the core project
  files under, `t3editor`, `RTE (rtehtmlarea + ckeditor)` and
  `Language Manager (backend)` name subsystems `.checkouts/main` no longer
  ships; every category on the reporting session's own page of stale Bugs names
  one it still does.
- "Browser-only" is not that no layer can hold it. Every covered branch has a
  browser layer — Codeception on 12.4, both on 13.4, Playwright alone on `main`
  — and each needs an installed instance and a browser before anything is seen,
  which is the cost rather than the absence.
- What the suite models is wider than a category suggests.
  `core/Tests/Functional/DataScenarios/` carries the flex, group, many-to-many
  and category constellations and a workspaces variant of each, so a relation or
  workspaces report is not expensive by its subject.

## Decided

- The skill states five readings under "Find the candidates", cheapest first:
  what already happened to the issue, the category against the branch, where the
  symptom appears, how far the mechanism reaches, and what the suite already
  models.
- They are written as what to read rather than as field names. The row that
  carries the first of them is a parallel change, and prose survives whatever
  that change calls its keys.
- Nothing is said about the tracker's own difficulty fields.
  [`D-ANS-069`](../answers/ans-069-a-backlog-row-carries-the-review-server-and-not-the-journal.md)
  measured them empty on exactly the backlog where the question arises, and a
  skill that names a field nobody fills spends a session's reading on it.
- The section ends by asking the answer to name which reading decided, and to
  say of the rows it passed over that they were passed over. Skipping is not
  triaging, and the escape hatch above it already owes a "why".

## Assumed

- One labelled set. Nine issues, one reporter, one page of stale Bugs, and the
  settleability of the other twenty-one is nobody's reading yet.
- The fifth reading rests on what `.checkouts/main` models today. A suite
  reorganisation moves it, and the skill says to look rather than what to find.
- The settled issue's patch was never read. Change 95182 answers `Not found` on
  the review server's own interface, bare and project-qualified, so what made
  #58705 cheap is read off the report and the checkout rather than off its diff.

## Wrong if

- A session picks by these five and still spends the session standing an
  installation up, which would say the third reading is being answered off the
  subject rather than off the report.
- The category reading never fires again over a real backlog, which would make a
  free reading a paid one.
- A candidate rejected as an interaction turns out to be one class after all,
  which would say the fourth reading trusts a reporter who was guessing.
