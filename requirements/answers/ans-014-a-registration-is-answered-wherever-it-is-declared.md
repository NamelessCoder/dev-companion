---
id: R-ANS-014
status: held
restsOn: [D-ANS-014, D-ANS-019]
---

# R-ANS-014 — A registration is answered wherever it is declared

**What `typo3_extension_scope` reports is every registration the extension
declares in a file that stands still, and not only the ones declared in the
files the answer was first built around.**

The answer lists fifteen kinds of registration. Three that a site package
ordinarily ships are in none of them, each declared statically and each
reachable from a file the answer already opens or already names:

- The FlexForm a content element binds, from the calls below
  `Configuration/TCA/Overrides/` — reported against the identifier it belongs
  to, because a content element with a FlexForm and one without are different
  things to review. A binding whose identifier no entry carries is reported
  apart rather than dropped.
- What a site set carries beside its name and path: the files core reads there
  by exact name, `route-enhancers.yaml` among them.
- The form storage an extension registers, both ways in, and the form
  definitions in it.

Which of those hold across the covered majors was established against
`.checkouts/` at 12.4, 13.4 and 14.3 before anything was written, and each of
the three moved inside that range:
[`D-ANS-019`](../../decisions/answers/ans-019-three-registration-kinds-read-from-what-core-reads-them-for.md)
carries the four call shapes, the eight file names and the two registration
ways, with what would show each wrong.

The boundary the entry does not cross is the file listing. Test files by path
and a walk of `Configuration/` are a tree, and this answer states what a tree
cannot: what a declaration means, and which of the four artifacts is absent.

## From

`feedback/2026-07-31-194510`, a conformance audit of a TYPO3 14 site package
that fell back to `glob` and `read` for the whole file tree. Re-run on
2026-08-02 against `printworks_sitepackage`: its two FlexForms, its
`route-enhancers.yaml` and its form set are in no answer this server gives, and
the two content elements the answer describes least are exactly the two whose
FlexForm it did not read.

## Held by

- `ProjectTest::theFlexFormAContentElementBindsIsOnItsEntry`
- `ProjectTest::aFlexFormBoundThroughACallThisDoesNotReadIsStillReported`
- `ProjectTest::aSiteSetIsAnsweredByTheFilesCoreReadsItFor`
- `ProjectTest::aFormSetIsAnsweredWithTheDefinitionsItStores`
