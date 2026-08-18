# Publish the distribution content skill

**Serves:** feedback/2026-08-17-212538-no-skill-owns-the-project-as-a-deliverable-or.md
**Priority:** normal
**Waiting on:** the review `writing-a-skill.rst` requires, and the baseline run
    `D-SKL-035` buys. The first is `SKILL.md` shown whole to whoever asked for
    the skill, asked by name which step is missing, which is wrong, and what it
    claims that is not true in their installation; what comes back is worked in
    before publication and not after, because the copy in somebody else's
    project is corrected by no release of this server. The second is the same
    person's for a different reason: the run needs two installations of the
    covered version and a client session driven in each, and this worktree has
    neither an `E-SITE` — `bin/cli environment:status` reports every one of them
    in the main checkout — nor the `.session-command` `todo:claim` starts a
    session with. Where such a run would be recorded is undecided as well:
    `scenarios/runs/` holds forward reviews, and
    `ScenariosTest::aTargetedContractCaseIsNotSomethingARunCanAnswer` asserts
    that a contract id has no skeleton at all.

The contract case is written: `SKILL-14`, stating the task in a user's words and
holding the four steps that report success while failing. It is `open` and turns
`held` in the commit that publishes the skill, because no assertion can guard
that a task reaches a workflow no client has.

Two of the three things publication owes are the reviewer's rather than a
session's, which is the correction to make before the next reading of this card:
the run is not a session's either, for the reasons in the line above.

Publishing is then one commit: the `metadata` declaration deleted, the intent
that routes to it written in `knowledge/task-intents.json`, the workflow named
in `knowledge/server-scope.json`, `SKILL-14` turned `held`, and the room found
in the listing.

## What the room costs, read on 2026-08-18

The twelve published descriptions stand at 3597 characters of 3600, and the
draft's own costs 374 with its name and separator. So publication owes **372
characters**, which is more than any one description carries.

Three of the twelve open with a step list rather than with the task, which is
what `D-SKL-024` cuts, and each keeps its trigger words when the steps go:

- `typo3-core-patch-checkout` — "Trying one out, checking whether it still
  applies, getting a checkout back onto a clean current branch" restates the
  first sentence's "and out again". Two triggers kept: **50**.
- `typo3-core-patch-development` — "reproduce the issue on the branch you are
  fixing, make the change, cover it, write the changelog entry, run the
  project's checks, and push to Gerrit" is the body's section headings in one
  line. Gerrit, the changelog entry and the checks kept: **68**.
- `typo3-core-issue-triage` — "find candidates in the backlog, read what the
  report claims, and establish against the core checkout whether it still
  happens, was fixed, or was never a defect" is the same shape. The checkout and
  the three verdicts kept: **79**.

That is 197. The draft's own second sentence repeats "initial content" from its
first and spends the rest on file names, which is worth about **66** more.

The remaining **109 characters have no candidate that is not a trigger word**.
Every other description spends its second half on the surfaces a user names —
TCA, Playwright, PHPStan, a site that will not come up — and `D-AUD-003` is what
cutting one of those costs. The two readings left for the publishing commit are
therefore the ones `D-SKL-054` named and neither is cheap: whether
`typo3-extension-cleanup` and `typo3-extension-conformance` are one workflow,
which would free 232 and which `D-SKL-014` reads as two; or whether 3600 is
still the right ratchet, which that entry's own **Assumed** says nobody has
re-measured against a client since 2026-08-08.
