---
id: D-DOC-031
date: 2026-08-12
status: open
---

# D-DOC-031 — A page is railed under a label and headed by a sentence

**A menu shows a page under a label of at most four words, said in a
`:navigation-title:` where the heading is not one, and the heading stays what it
was.**

A menu entry is read at a glance and in a column two words wide, and this corpus
titles a page with the question it answers.

## Evidence

- The footer sitemap showed the headings:
  `The draft RFC on an MCP interface contract for TYPO3` wrapped to three lines,
  `Writing a decision, and going back to one` and
  `Which versions an answer holds for` to two.
- The bar says `Usage`, `Server`, `Contributing` and `Records` — `D-DOC-025` —
  while the same four sections read `Using the server`, `The server`,
  `Contributing` and `What is written down, and where` in the rail and the
  footer. One section under two names is what a reader has to resolve first.
- The generated tool pages needed nothing: a page titled after the tool it
  documents is already one word.
- `_search.json` carries the heading, not the navigation title, so the sentence
  is what a search still matches on.

## Decided

- The field sits above the heading, where the front page's `:layout:` sits —
  written below it, the parser renders it into the body as a definition list.
- Four words is the boundary, counted on the navigation title where there is one
  and on the heading otherwise. It is what the corpus already wrote: of the 25
  pages nothing generates, 17 were inside it and 8 were the sentences above.
- A page whose heading is inside the count is labelled anyway where the label is
  the shorter name for the same thing — `Knowledge base` for
  `The bundled knowledge`, `Decisions` for `What a decision is`. What is guarded
  is the count; the rest is that a menu says the name and the page says the
  sentence.
- A section page takes the bar's word exactly, so the sitemap, the rail and the
  trail say `Server` where the bar does.
- The heading is left alone rather than shortened. It is the sentence the page
  opens on and what the search index reads, and shortening both would have cost
  the corpus the questions its pages are titled with.

## Assumed

- That a reader picks a page from a label and reads the heading afterwards.
  Nothing here measures it.
- That the label and the heading naming the same page differently is not itself
  confusing — `Version binding` in the rail,
  `Which versions an answer holds for` on the page.

## Wrong if

- A page cannot be found because its label dropped the word somebody was looking
  for.
- A four-word label is still a sentence, which the count cannot tell.
- The theme starts showing the navigation title where the heading is meant, and
  a page then announces itself as `Tools`.

## Covered by

- `SiteTest::everyPageIsRailedUnderALabelRatherThanItsHeading`
