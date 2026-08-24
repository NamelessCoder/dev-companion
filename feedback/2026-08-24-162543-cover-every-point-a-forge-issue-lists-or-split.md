---
date: 2026-08-24T16:25:43+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_forge_lookup, typo3-core-patch-development
directory: /home/benji/projects/typo3-cms
---

# Cover every point a Forge issue lists, or split it into separate issues up front

## Observation

Task: find an easy-to-fix Forge issue in the asset renderer area and fix it with tests. I picked #106584 ("Re-add the manual argument registration for href and src"), whose subject names two ViewHelpers but whose comments extend the scope to three: f:asset.css -> href, f:asset.script -> src, f:image -> alt, confirmed by maintainer Simon Praetorius in note-6.

I implemented only href and src, and reported f:image alt as a follow-up "needing its own issue", because registering alt shifts the attribute order in the rendered img tag. The user corrected this: either implement all points an issue lists, or split it into separate issues explicitly and up front — never silently narrow to the convenient subset and mention the leftover as a footnote.

The reasoning that makes this a rule rather than a preference: in Gerrit every commit is reviewed, merged and backported on its own, and the Resolves: trailer is what closes the issue. A patch closing an issue while covering only part of it leaves the remainder invisible, because nobody reopens a closed issue. Deciding the split before writing code also matters practically: each part then needs its own issue number, which is required in two places (the commit trailer and the RST changelog filename).

A second observation from the same task: the scope-defining information was in the issue comments, not in the subject or description. typo3_forge_lookup returns comments, but nothing in the tool description or in the patch-development skill flags that the comments regularly redefine what "done" means for an issue.

## Query

typo3_forge_lookup issue=106584 notes=people, followed by writing a core patch for the issue

## Suggestion

In the typo3-core-patch-development skill, add an explicit scoping step before any code is written: read the issue including its comments, enumerate the points it requires, and decide either "one commit covers all of them" or "this becomes N commits and needs N issue numbers" — stating that decision to the user up front. Make clear that a part being riskier (visible output change, own changelog category) is an argument for splitting it into its own issue, not for dropping it from the patch.

In typo3_forge_lookup's description, state that an issue's effective scope is frequently set in the comments rather than in the subject or description, so a maintainer's note can add ViewHelpers, classes or cases the subject never mentions — and that the comments must be read before scoping a patch, not only when triaging.
