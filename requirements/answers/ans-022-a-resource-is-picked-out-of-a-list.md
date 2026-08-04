---
id: R-ANS-022
status: open
restsOn: [D-AUD-004]
---

# R-ANS-022 — A resource is picked out of a list

**A `typo3://` resource is picked out of a list, so it says what it is and what
it obliges, and every audience has one to pick.**

Resources are application-driven: the host offers them for selection, or the
user searches them, where a tool is called by the model in the middle of a task.
The list is therefore the whole of what the choice is made on, and the spec says
what a client reads it by — `description` to understand what it is being
offered, `annotations.priority` to sort what is worth the context, `size` to
know what reading one costs. A resource offered as a name and a mime type is one
nobody picks.

Who does the picking is the second half. The tool list serves the three
audiences of [`R-AUD-001`](../audience/aud-001-three-audiences-not-one.md); the
document corpus behind `typo3://core/{id}` serves one, because four of its five
documents are the core repository's own and say so. An extension author and a
site developer are offered the commit conventions and nothing else, which is a
surface that describes itself correctly and still has nothing for them.

That is why this entry is **open** rather than held. The metadata is built and
the documents are not, and a requirement that claimed otherwise would be true of
the half that was easy.

## From

The reading of 2026-08-04 that found no file in `requirements/` naming
`typo3://` at all, and the answer that settled what the surface is for: it
serves all three audiences rather than being the core-contribution corpus it is
today. The three options for closing that, and what each costs, are on the card
in [todo/waiting/](../../todo/readme.md).

## Held by

- `ResourceSurfaceTest`, for what a client reads before it picks: every resource
  describes itself, says who its answers oblige, and declares the size and the
  priority a picker sorts by
- `StdioServerTest::theResourceListCarriesWhatAPickerChoosesBy`, on the list as
  it goes over the wire
- Nothing holds the audiences, and no test can: what is missing is a document
  nobody has decided to write.
  `ScopeTest::everyKnowledgeDocumentIsAnnouncedByTheScope` records which of them
  stop at the core, and recording is not demanding.
