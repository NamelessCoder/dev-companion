---
id: D-SKL-063
date: 2026-08-18
status: open
---

# D-SKL-063 — Reviewing one change proposed against a package that is not the core earns a task skill

**A change proposed against a package that is not the core is judged by a task
skill of its own, which stops at the verdict.**

A pull request, a patch and a branch are the forms it arrives in. The core side
has had that workflow since `typo3-core-patch-review`, and the extension side
has one skill for auditing a whole repository and one for carrying findings into
commits, with nothing between them that takes a single incoming diff as its
object.

## Evidence

- `feedback/2026-08-18-113343`, from a session in `bootstrap_package`. The
  request arrived as "review mir bitte den patch
  https://github.com/benjaminkott/bootstrap_package/pull/1627" — a GitHub pull
  request, one file, one line, against a sitepackage. The session names all
  three candidate skills and why each was rejected from its description, then
  reports that it worked the review out itself and would do so the same way
  again. That is the first signal `judging.rst` gives for a missing skill: a
  session that invented the right order itself.
- The descriptions arrived and were read, so this is not delivery.
  `feedback/archive/2026-08-18-113308`, from the same session, reports that
  every MCP tool of this server arrived as a bare name under schema deferral and
  that not one was called; the skills were listed with their descriptions, which
  is what the session quotes back.
- `typo3-extension-conformance` is wrong for the object in its body and not only
  in its description. Read on 2026-08-18: step 3 writes the surface list from
  `references/checklist.md`, "narrowed to the ones this kind of checkout can
  have", and step 4 narrows further only where the request names a surface — "A
  request that names no surface is not a focused one, and every entry is read".
  A one-line diff names none, so what a session routed there would run is a
  whole-repository audit. It also assesses against the active installation,
  which a review of somebody else's pull request has no reason to have.
- Three of the five things the review had to establish are on no audit surface:
  whether the change holds on both sides of the version range the package's own
  Composer constraint declares, whether the commit message matches the
  repository's convention rather than the core's, and whether CI is green and
  the branch mergeable.
- `typo3-core-patch-review` owns this task shape and binds it to the other
  process. Its description says "your own before you push it, or somebody else's
  patch set", and what its body reviews includes the changelog entry, the issue
  reference and the target branch — properties of a Gerrit change, none of which
  a pull request against a sitepackage has.
- `bin/cli hints:probe "review a pull request against an extension repository"`
  reaches `extension-repository-layout` and `extbase-domain-mapping` and nothing
  about judging a change. Outside the core's own contribution documents, nothing
  in `knowledge/` or `skills/` names a pull request at all.

## Decided

- Step 1b of the ladder, and taken on. The answers are all available — the
  conventions lookups, the changelog, the extension description, the commit
  message guide — and nothing says in which order to ask for them when the
  object is one incoming change.
- A skill of its own, rather than a widening of either neighbour. Reaching a
  pull request from conformance's description is the failure `D-SKL-016`
  recorded against that same file, where "improve" pulled change requests into
  the workflow that makes none; here it would pull a one-line diff into a
  whole-repository audit. Widening `typo3-core-patch-review` puts two premises
  in one description, and `D-SKL-061` is what an opening clause that governs the
  wrong premise costs.
- The boundary. It owns judging one proposed change against a package that is
  not the core, and stops at the verdict. The whole repository is
  `typo3-extension-conformance`'s, carrying findings through to committed
  changes is `typo3-extension-cleanup`'s, and anything on review.typo3.org is
  `typo3-core-patch-review`'s.
- It is written as a draft under `typo3-dev-companion-status`, so the listing
  budget `D-SKL-026` measures is asked at publication rather than of the file
  being written — `D-SKL-054`.
- Rejected: closing this on conformance's focused-review clause. That clause
  narrows on a surface the request names, and a pull request URL names none.
- The feedback stays open. What it asks for lands in somebody else's project,
  which is the far side of what a judging run may write on the spot.

## Assumed

- That the five checks the feedback lists are what such a review is, rather than
  what one review of a one-line TCA change happened to need. Nothing here has
  measured a second one, which is why the research `writing-a-skill.rst` asks
  for is the todo's first step and not a formality.
- That one session's report plus the asymmetry above carries the bar
  `writing-a-skill.rst` sets. The corpus holds no second sighting: what stands
  in for it is that the core side has this workflow and the extension side has
  every other one.

## Wrong if

- The research finds the five checks are already what conformance's checklist
  covers once its surface list may be derived from a diff. Then this is step 4
  on that skill, and one paragraph rather than a thirteenth file.
- A draft is written and no session loads it, because a pull request review is
  worded "look over this change to my sitepackage" as often as it names the pull
  request — which is wording conformance already reaches.
- The baseline run `D-SKL-035` buys shows a session without the skill
  establishing the same five things anyway. The feedback's own session did
  exactly that, and if it repeats, the skill is selling an order nobody was
  missing.
- The listing budget at publication leaves no room, and what the twelve are
  worth turns out to be more than what the thirteenth adds.

## Since then

The research `writing-a-skill.rst` asks for was carried out on 2026-08-18, by
putting the five checks to the tools themselves rather than reading their
descriptions. The first **Wrong if** did not fire: conformance's checklist is
written per surface of a whole package, and three of the five reach none of
those surfaces. What the calls moved is the order. `typo3_project_describe`
already carries the constraint the package declares, so the version axis costs
no call of the skill's own and is written as a discharge. The two compatibility
documents own the second check whole and are read by `documentId` rather than
searched, and `typo3_changelog_lookup` narrows it without closing it — asked
where no installed core ships the changelog it says so, which is not an empty
answer. The fourth check turned out to be two steps rather than one:
`typo3_commit_message_guide` with `workflow="project"` holds a message to
TYPO3's keyword convention and answers a message written in another one with an
error, so which convention the repository writes is established from its own log
first. The fifth is not this server's at all — whether the branch merges and
what the pipeline said are the forge's, and what the review can add is the
checks the repository declares, run here.

The draft is `skills/typo3-extension-patch-review/SKILL.md`. Two things at
publication are not settled by any of that: the intent that routes to it, which
`typo3_task_guide` today answers with `typo3-extension-conformance` for a review
of one diff against an extension, and the listing budget.
