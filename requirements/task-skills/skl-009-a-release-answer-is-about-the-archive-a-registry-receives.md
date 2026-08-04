---
id: R-SKL-009
status: open
---

# R-SKL-009 — A release answer is about the archive a registry receives

**What this server answers about a release is about the archive a registry
receives and the mechanism that built it, not about the checkout.**

A checkout that passes every check it declares establishes nothing about what
ships. The archive is a different set of files, chosen by a mechanism that is
not the working tree and is not the same mechanism for every registry: a
version-control export honours the exclusion attributes committed beside the
files, while a registry's own packaging tool applies an exclusion list shipped
with the tool and never reads those attributes. Both are correct about their own
rules, and one commit hands two registries two different file sets with nothing
in the repository reporting it.

That difference is readable only between the archives. Against the working tree
the two mechanisms agree by construction, and against one archive alone there is
nothing to compare it to. Which mechanism left a file out, and where the list
that decided it lives, is part of the difference rather than background to it —
a rule the maintainer cannot find is a rule they cannot fix.

What a workflow around that owes is not settled, and this entry does not settle
it. Four questions are open: what such a task is for at all — carrying a release
out, describing the procedure so the maintainer does it, or setting the release
tooling up; where it stops, since a tag, a push or an upload changes state other
people depend on; whether the commands the project declares as checks are run
against the candidate; and whether the package is resolved from its declared
dependencies somewhere other than the working tree, which already has them.
Nothing here has measured or decided any of the four.

Extension release is intended to come back. The piece being worked first is
[the card](../../todo/open/2026-08-04-140100-work-out-how-typo3-tailor-is-installed-and-set-up-for-an-extension.md),
which is `typo3/tailor` setup for an extension rather than the whole subject.

## From

The feedback of 2026-07-30 17:44 and its re-run in `E-EXT` on 2026-07-31, seven
commits past a tag: no skill activated and no tool was called across forty-one
`Bash` calls, because not one of the six published skills carried the words
*release*, *publish*, *registry*, *artifact*, *archive* or *tag*. The run
established the gap it was asked to name — the export and the registry tool's
archive shipped different file sets out of one commit, the difference being
tracked editor configuration that `git archive` dropped for an `export-ignore`
attribute and `tailor create-artefact` kept, because it filters by its own
`conf/ExcludeFromPackaging.php` and never reads `.gitattributes`. No check in
that green checkout said so. The counts the report gives, 1558 against 1559, do
not square with the two files it names as the difference; the mechanism is what
the run established and the arithmetic is not re-checkable from here.

## Held by

Nothing. No skill orders this task, no tool answers it, and no scenario states
what a session would have to produce.
