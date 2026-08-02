---
id: R-DOC-003
status: held
restsOn: [D-ANS-023]
---

# R-DOC-003 — A ViewHelper question is answered from the manual that documents ViewHelpers

**What a ViewHelper does is answered from the reference that documents
ViewHelpers, at the version it was asked for.**

`R-DOC-001` says the manuals searched are the ones a question can be about.
This names the one that was missing and what carrying it costs. The Fluid
ViewHelper Reference is published under `/other/` rather than the `/m/` the
three manuals of the core sit in, so where a manual is is the collection it is
published in and not one base built for all of them. A base built wrong is
silent: the root does not answer, the book is simply absent from the index, and
the question comes back answered from whichever of the other manuals carries
the word. So what has to hold is that its pages are in the index, that they are
there under its own base, and that a URL handed back from it is one the same
call takes back.

What this does not promise is the page of a ViewHelper whose name is too short
to be searched for. `f:if f:then f:else condition ViewHelper` reaches
`Global/Else.html` and not `Global/If.html`, because `TermSearch::terms()`
drops every word under three characters and `then` is a stopword. That is a
limit of the tokenizer both corpora go through, and it carries a todo of its
own.

## From

`feedback/2026-08-01-003000`, judged as `D-ANS-023` on 2026-08-02. One session
lost a task to three Fluid mistakes; three manuals were searched, none of them
documents a ViewHelper, and `f:if f:then f:else condition ViewHelper` came back
with Developing a custom ViewHelper and the Translate ViewHelper.

## Held by

- `DocumentationTest::aViewHelperQuestionReachesTheManualPublishedOutsideTheCoreCollection`
- `DocumentationTest::aPageOfThatManualIsReadBackAtItsOwnBase`
