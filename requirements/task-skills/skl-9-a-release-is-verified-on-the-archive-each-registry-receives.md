---
id: R-SKL-9
status: held
restsOn: [D-EVI-2]
---

# R-SKL-9 — A release is verified on the archive each registry receives

**Preparing a release is verified against the artifact that would be published,
per registry in scope, and it stops before tagging, pushing or publishing until
that is explicitly asked for.**

A checkout that passes every check it declares establishes nothing about what
ships. The archive is a different set of files, chosen by a mechanism that is
not the working tree and is not the same mechanism for every registry: a
version-control export honours the exclusion attributes committed beside the
files, while a registry's own packaging tool applies an exclusion list shipped
with the tool and never reads those attributes. Both are correct about their own
rules, and one commit hands two registries two different file sets with nothing
in the repository reporting it.

So the reading that finds this is a comparison between the archives, and it is
the one this workflow owns. Against the working tree the two mechanisms agree by
construction; against one archive alone the difference is not visible at all.
What each mechanism excluded, and where that list lives, is part of the finding
— a rule the maintainer cannot find is a rule they cannot fix.

The boundary at publication is the second half. Building and reading an artifact
is local and reversible; a tag, a push or a registry upload changes state other
people depend on and cannot be undone by this workflow. An unclear target is
therefore a question rather than an assumption, because it is the one place
where continuing on an assumption publishes it.

**From:** the feedback of 2026-07-30 17:44 and its re-run in `E-EXT` on
2026-07-31, seven commits past a tag: no skill activated and no tool was called
across forty-one `Bash` calls, because not one of the six published skills
carried the words *release*, *publish*, *registry*, *artifact*, *archive* or
*tag*. The run established the gap it was asked to name — 1558 files in the
export against 1559 in the registry tool's archive, the two extra being tracked
editor configuration — and no check in that green checkout said so.

**Held by:** `SkillTest::aReleaseVerifiesTheArtifactAgainstEachRegistrysOwnExclusions`,
`SkillTest::everySkillStartsFromTheBaseBeforeItsOwnEvidence`,
`SkillTest::everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder`, `SKILL-10`.
That a session does the comparison rather than reading one archive is not
guarded: `D-EVI-2` accepts that proxy for what a skill file states, and no
forward review grades the file a run came out of.
