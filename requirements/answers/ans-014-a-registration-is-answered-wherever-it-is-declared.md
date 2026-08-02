---
id: R-ANS-014
status: open
restsOn: [D-ANS-014]
---

# R-ANS-014 — A registration is answered wherever it is declared

**What `typo3_extension_scope` reports is every registration the extension
declares in a file that stands still, and not only the ones declared in the
files the answer was first built around.**

The answer lists fifteen kinds of registration. Three that a site package
ordinarily ships are in none of them, each declared statically and each
reachable from a file the answer already opens or already names:

- The FlexForm a content element binds, from the `addPiFlexFormValue()` call
  below `Configuration/TCA/Overrides/` — reported against the identifier it
  belongs to, because a content element with a FlexForm and one without are
  different things to review and the answer today shows neither.
- What a site set carries beside its name and path: the files core reads there
  by exact name, `route-enhancers.yaml` among them.
- The form storage an extension registers, and the form definitions in it.

Which of those hold across the covered majors, and whether the list is these
three or longer, is established against `.checkouts/` before anything is
written — none of it is settled by this entry.

The boundary the entry does not cross is the file listing. Test files by path
and a walk of `Configuration/` are a tree, and this answer states what a tree
cannot: what a declaration means, and which of the four artifacts is absent.

## From

`feedback/2026-07-31-194510`, a conformance audit of a TYPO3 14 site package
that fell back to `glob` and `read` for the whole file tree. Re-run on
2026-08-02 against `printworks_sitepackage`: its two FlexForms, its
`route-enhancers.yaml` and its form set are in no answer this server gives,
and the two content elements the answer describes least are exactly the two
whose FlexForm it did not read.

## Held by

`not guarded`. Nothing yet reads an extension for a FlexForm, a site set's
files or a form set, so there is no assertion to name — the test comes with the
change, beside the ones in `ProjectTest` that already hold the override files
this reads from.
