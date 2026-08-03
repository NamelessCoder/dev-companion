---
name: typo3-core-patch-development
description: Write a TYPO3 core patch and carry it to review — assess the issue, reproduce it against the branch you are fixing, make the change, cover it, write the changelog entry it owes, run the project's checks, and push it to Gerrit. Use for fixing a core bug, implementing a core feature, deprecating or removing core API, taking a Forge issue on, amending a patch after review, and backporting a change to a release branch. Changing an extension, a sitepackage or a site project is a different workflow, and reviewing a patch without writing it belongs to the core patch review skill.
---

# TYPO3 Core Patch Development

Carry one change from an issue to a patch somebody can review. Keep this skill
as routing and working order; the suites, the scripts, the contribution rules
and the Gerrit commands are lookups, and a copy of them here goes stale in
somebody else's checkout with nothing to report it.

## Establish the issue before you believe it

1. Work through [references/base.md](references/base.md), which fixes the order
   every task here starts in.
2. `typo3_rule_lookup` for what this kind of change owes — a bugfix, a feature,
   a deprecation and a removal are four different sets of obligations, and which
   one you are in decides the changelog entry, the commit subject and the target
   branch.
3. Read the issue itself and treat it as a report rather than as a
   specification. An issue can be stale, half fixed, or right about the symptom
   and wrong about the cause, and the maintainers' comments on it can be product
   judgement rather than an API fact. Establish which of those you have before
   writing code: what the reporter saw, what the branch does today, and what the
   project intends the API to be for.
4. **Verify in the checkout every rule the issue quotes.** A rule about what an
   API may or may not be used for is a claim, the way a path or an identifier
   is. Read the class it names, its docblock and the core's own tests for the
   form under dispute, and say which of the three carries the rule. Enforced in
   code, warned about as fragile and advised in prose are three different
   claims, and two neighbouring APIs regularly make different ones. Carry it at
   the strength its own source puts on it. An assessment that hardens "may
   change in a future version" into "must not" argues the patch away on a rule
   nothing holds.
5. **Reproduce against the branch you are fixing**, not against the version in
   the report. Half of what a stale issue describes is usually gone, and the
   half that remains is the patch.

Whether that reproduction can be a test is a property of what you are changing,
and `typo3_test_run_guide` with the paths you are about to touch is what says
so: it names the suites that can fail on them, and a change to backend markup, a
build step or shipped JavaScript may have none that can hold the bug.

Where there is a layer, write the test first and prove it fails before the fix
and passes after it, in that order. A test written afterwards asserts what the
code now does, which is true of any code. Where there is none, reproduce by hand
and write down the steps and what you saw, because that is what a reviewer
repeats — an unreproducible claim is what sends a patch back regardless of
whether it is right.

## Make the change

Ask `typo3_hint_lookup` with the concrete paths for the conventions of each
subsystem you touch, before writing rather than after. A convention fetched
afterwards confirms what you already wrote.

Keep the patch one change. What else you noticed is another issue and another
patch; a diff that fixes two things is a diff a reviewer has to accept or reject
as one.

Find out whether the area is moving before you build on it, and fetch and rebase
onto the branch you target before you finalise. A patch written against code
that changed underneath it is not a patch that needs adjusting — the method it
called can be gone, and with it the reason the change looked right.

## Verify with the project's own commands

`typo3_test_run_guide` with the changed paths returns the suites that can fail
on this change, each with its targeted invocation, and `typo3_script_lookup`
returns the scripts around them. Run the narrow ones while iterating and the
broad ones before pushing.

Two things decide whether that verification means anything:

- The runner is the project's. A suite run through the host's own PHP or an
  installed binary rather than through the project's runner is a green nobody
  can reproduce.
- A green that ran over no files is not a green. Where a check reports success,
  confirm it inspected something — the count of files, tests or fixtures it
  names — before treating it as evidence. A check that silently found nothing to
  check is the failure mode that survives review.

## The changelog entry the change owes

`typo3_rule_lookup` says which changes need one, what it is named, and where it
lives. Decide it from the change type rather than from habit: writing an entry
for a change that owes none is as much a review finding as omitting one that
does.

## Commit and push

`typo3_commit_message_guide` with the drafted message and the change type
reports what is still wrong with it before the hook does. Then
`typo3_rule_lookup` for the Gerrit workflow: what the push actually is, how a
change is amended into a new patch set rather than a second commit, and what
must not be edited between patch sets.

Before pushing, establish where you are pushing to. A core checkout's remote is
not necessarily the one it fetches from, and the answer is in the checkout's own
git configuration rather than in what the repository is called.

Pushing is a step of its own and is taken when it is asked for. Everything above
is local and reversible; the push is neither.

**Ask whether the change goes up visible to everyone or unlisted, every time.**
The two are different refspecs and the difference is not a preference: one
publishes the change to whoever watches the project and notifies reviewers, and
neither the publication nor the notification is quietly undone.
`typo3_rule_lookup` for the Gerrit workflow has both forms. Which one this
change wants is the user's decision and never a default read off what the
session did last.

## Amending after review

A patch that came back is the same change, not a new one. Fetch the patch set
that exists, amend it, and keep the identifier that links it to its review —
`typo3_rule_lookup` for the Gerrit workflow says how each of those is done.
Reviewer comments are addressed in the patch or answered in the review, never
silently dropped: a comment nobody replied to is the reason a change sits
unmerged.

This skill owns writing a core patch and delivering it — the change itself, its
tests, its changelog entry, the checks it has to pass, its commit message and
its push. It does not own judging somebody else's patch, and it does not own
judging its own: where the request is to say what is wrong with a change rather
than to make one, `typo3-core-patch-review` owns that and reads different
surfaces for it. Take its findings as a work list when it hands them over.
Changing an extension, a sitepackage or a site project belongs to the extension
skills, whose conventions are not the core's.
