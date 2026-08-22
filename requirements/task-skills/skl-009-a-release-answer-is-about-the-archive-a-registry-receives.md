---
id: R-SKL-009
title: 'A release answer is about the archive a registry receives'
status: open
judged: 2026-08-22
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

Extension release is intended to come back, and what it comes back as is open.
`typo3/tailor` setup was worked first and turned out not to be the piece: what
it asks of an extension is four facts, carried by `extension-ter-release` in
`knowledge/hints/extension.json`, and nothing in them is an order a workflow
would keep. The card that carried the question was put to the maintainer on
2026-08-04, 2026-08-12 and 2026-08-19, and the third answer deleted it. Whether
the release run earns a skill instead is open and nothing queues it: the run
stops short of `ter:publish`, which needs a TER token nothing here holds, so
what would revive the question is a filed session bringing the wording or
somebody wanting the release driven end to end.

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

The mechanism was reproduced outside that checkout on 2026-08-04, against
`typo3/tailor` 1.7.0 installed into a scratch project and run over a fixture
extension: an `export-ignore` attribute on a tracked directory took it out of
`git archive` and left it in the artefact, and no occurrence of `gitattributes`
or `export-ignore` exists anywhere in Tailor's source outside the filename it
excludes from packaging. The same fixture established that the exclusion list
does not mean what it reads as. Its entries are interpolated into patterns
unquoted — directories matched `/^<entry>/i` against the path relative to the
extension root, files `/<entry>$/i` against the filename alone — so a top-level
directory whose name merely begins with a listed one is dropped, `binx/` and
`publicity/` going out for `bin` and `public`, while the same name nested deeper
survives; and any regular-expression character in an entry keeps its meaning,
the `.` in `phpstan.neon` taking `phpstanXneon` with it. That is a second way
one commit hands two registries different file sets, and it is readable neither
in the archive nor in the list a maintainer would go and read.

## Held by

Nothing. No skill orders this task, no tool answers it, and no scenario states
what a session would have to produce.
