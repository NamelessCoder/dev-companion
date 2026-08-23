---
id: D-DOC-029
title: The documentation is reStructuredText, and the rest of the corpus is not
date: 2026-08-12
status: open
coveredBy:
  - LinksTest::everyPathThisRepositoryWritesToItselfResolves
  - ProseTest::aLiteralAndARoleAreNeverBrokenAcrossLines
  - ProseTest::rewrappingChangesNothingButTheLineBreaks
  - ProseTest::whatIsNotProseInReStructuredTextComesBackUnchanged
  - SiteTest::aDirectorysOwnPageIsPublishedAsItsIndex
  - SiteTest::aReferenceInsideTheCorpusIsNotRewritten
  - SiteTest::everyDirectoryOfTheDocumentationHasItsOwnPage
  - SiteTest::everyReferenceIntoAnotherPageIsAnsweredByALabel
  - ToolAnswersTest::everyCallOnAPageCarriesItsArgumentsAndItsAnswers
  - ToolSurfaceTest::theIndexReachesEveryToolAndTheDirectoryHoldsNoOther
---

# D-DOC-029 — The documentation is reStructuredText, and the rest of the corpus is not

**`documentation/` is written in reStructuredText and published as such;
`decisions/`, `requirements/`, `todo/`, `scenarios/`, `skills/` and
`knowledge/documents/` stay markdown. What reads the prose corpus asks the file
which of the two it is.**

The site is what `documentation/` exists to be — `D-DOC-026` — and it was being
written in the markup that could say the least about it. A cross-page reference
was the case that decided it: markdown could not make one, so `Site` threw the
fragment away and landed the reader at the top of a page instead.

## Evidence

- The renderer resolves `:ref:` across pages and fails the build where a label
  is missing. Measured against the real theme on 2026-08-12:
  ``:ref:`over here <deep-anchor>``` in one page renders as
  `sub/page.html#deep-anchor` in another. `Site::page()` used to drop such a
  reference whole because CommonMark's own resolution discarded text and link
  together, and 33 links into `answer-sources.md` were landing a segment away
  from what they named.
- 81% of the corpus is generated. 26 tool pages hold 16,988 of 20,854 lines and
  are written by `ToolSurface` and `ToolAnswers`, so the conversion was a change
  to two classes rather than to 26 files. The 25 hand-written pages were 3,866
  lines.
- The recorded halves could not be re-recorded. `tools:record` refuses without
  `.checkouts/`, and this repository is the only place that evidence exists, so
  the 8,481 recorded lines were transformed in place and the 8,533 derived ones
  regenerated.
- `phpdocumentor/guides-cli` registers `ReStructuredTextExtension` itself, so
  the `MarkdownExtension` line in `guides.xml` went with the format. The
  `CodeExtension` line beside it did not and cannot: the theme's own
  `body/code.html.twig` colours every block with a `highlight` filter only that
  extension registers, whatever the input format is.
- The four blocks that leaked in `D-DOC-007` cannot happen here. A directive's
  content is what is indented under it, so a recorded answer that is itself a
  document has no closing marker to imitate, and the backtick-counting in
  `ToolAnswers::fenced()` is gone.

## Decided

- A directory's own page is `readme.rst` here and `index.rst` published, as
  before. A `:doc:` is written against the published name, and `Links` maps
  `index` back to `readme` when it resolves one in the checkout.
- Inside the corpus, a page is reached by `:doc:` and a place in one by `:ref:`.
  `Site` rewrites neither: the renderer resolves both, so the only link it
  touches is the embedded form, which is the only one that can leave the tree.
  `relative()` and the fragment-dropping branch went with that.
- Every tool page carries a label of its own name, and every section of
  `answer-sources` carries `answer-sources-<source>`. A reference is global in
  reStructuredText, so a label says which page it belongs to.
- The rail is a `toctree` in each `index`, written by hand for the four sections
  and by `ToolSurface` for the tools. `automatic-menu` derived the same thing
  from the directories because the Markdown parser had no directive to say it
  with; an explicit tree says the order as well.
- `Wrap` gets a second document reader rather than exceptions in the first. The
  two markups break on different things, and what may be rewrapped in
  reStructuredText is the smaller set: a heading is a line and the rule under
  it, a directive owns its indent, a table is drawn to a column.
- `Rst` holds the emission — the underline per level, the directive, the
  literal, the two roles — because two generators write pages and an underline
  character is exactly the convention that drifts when it is agreed rather than
  declared.

## Assumed

- That the sources are read on the site and not on GitHub. A `:doc:` and a
  `:ref:` render as nothing there, so a reader who opens
  `documentation/records/judging.rst` in the repository sees the references as
  literal text. `D-DOC-026` says the site is the documentation, and this rests
  on it.
- That two markups in one prose corpus cost less than they buy. Everything that
  reads the corpus — `Links`, `Prose`, `Wrap`, `prose:format` — now branches on
  the extension, and a session writing in both switches between them.

## Wrong if

- Somebody writes markdown into `documentation/` and nothing says so. The
  renderer would fail the build, but only where the file reaches a render; a
  page nothing includes is an orphan warning and no error.
- The two `Wrap` readers drift. `document()` and `rst()` share the wrapping and
  nothing else, and a fix made in one is a fix the other does not get.
- The references go stale in the other direction: a label renamed with the
  heading above it, where `:ref:` names the label and no reader sees both.
  `Links::deadLabels()` is what would catch it.
- A reader arrives from `AGENTS.md` or a decision, lands on a `.rst` file in the
  checkout, and cannot follow it further because every link on the page is a
  role. That is the assumption above, failing.
