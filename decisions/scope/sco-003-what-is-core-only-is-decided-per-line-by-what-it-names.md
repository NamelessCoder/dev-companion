---
id: D-SCO-003
date: 2026-07-29
status: confirmed
---

# D-SCO-003 — What is core-only is decided per line, by what it names

**Whether a line is core-only is a mechanical check over the rendered text
rather than a flag on each entry.**

`typo3_task_guide` now drops core-only material outside the core. What counts
as core-only is not a flag on each entry but a check on its text: does it name
something that exists in the core repository and nowhere else —
`typo3/sysext/`, `Build/Scripts/`, Gerrit, a Change-Id, the core branch policy.

## Decided

- A mechanical check over the rendered line, in `Scope`, applied to the
  checklist, the checkout discovery steps and the follow-up tools. The
  alternative — marking every checklist item, every intent item and every scope
  entry in the knowledge files — is a flag on a hundred strings that has to be
  set correctly each time one is added, and forgetting it fails silently.

## Assumed

- Naming a core artefact is a reliable proxy for being unusable outside the
  core, and the cost of the two error directions is asymmetric: a transferable
  line dropped because it mentioned a core path as an example is a smaller loss
  than an unrunnable command handed over as a step.

## Wrong if

- A checklist item has to survive although it names a core path — advice about
  reading the core as a reference rather than changing it would be exactly
  that. It would then need the flag after all.

## Confirmed on 2026-08-02

The **Wrong if** has not happened. Read out of the three corpora the check runs
over in `TaskGuide` — the 50 checklist lines a brief can put in front of it,
the 6 checkout discovery steps, the follow-up tools — six drop, and all six
instruct writing into the core or pushing to it: the three changelog files
below `typo3/sysext/core/Documentation/Changelog/`, the `checkRst` run through
`Build/Scripts/runTests.sh`, and pushing to `refs/for/`. Not one is advice
about reading the core, so the shape that would need the flag is not in the
corpus and the mechanical check keeps its case. The nearest thing to it is a
discovery step rather than a checklist item, and it is worth naming because the
next session will find it and stop there: the icon step belongs to everyone —
an identifier that is not registered renders an empty box in any repository —
and it is dropped whole for the by-hand fallback it ends on,
`typo3/sysext/core/Resources/Public/Icons/T3Icons/icons.json` and the `Flags/`
directory beside it. Dropping it costs nothing today, on two counts. The
transferable half arrives twice regardless, from lines that carry no marker:
`typo3_icon_lookup` in the follow-up tools, and the `icons` intent's own
checklist line, which says to resolve the identifier in your own checkout. And
the fallback would not have been runnable there anyway — read in
`.checkouts/13.4`, regular Composer mode never creates `typo3/sysext/` at all.
`Core\Composer\InstallerScripts` registers that entry point for `typo3/cms`
alone, the core's own monorepo, and everywhere else `PackageArtifactBuilder`
publishes a package's public resources to `<web-dir>/_assets/<md5>` instead.
That is the **Assumed** holding for the marker that carried all six drops. What
is left to watch is the second count rather than the first: a line of the
reading shape is survivable while its transferable half is carried by a line
without a marker, and nothing asserts that pairing.
