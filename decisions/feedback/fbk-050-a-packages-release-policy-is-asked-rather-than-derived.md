---
id: D-FBK-050
date: 2026-08-19
status: open
---

# D-FBK-050 — A package's release policy is asked rather than derived

**A package's release, merge and backport policy is asked of the maintainer, and
the rule that says so is delivered by the skill that lands a change.**

`feedback/2026-08-18-113441` asks `typo3_project_describe` for it instead, out
of a branch and tag pattern where nothing is written down. The session that
filed it had already run those git commands, and what they returned is what
nearly sent a fix to an unsupported line.

## Evidence

- The cost is in the feedback and it came after the fact: the rules arrived from
  the user halfway through, by which time a branch was pushed and a pull request
  opened, and five maintenance branches had to be surveyed to work out which the
  second fix reached.
- The session's own reading refutes the shape it asks for. It saw `BP_6-2`
  through `BP_16_0` in the remote refs and read `git log --merges`, and it wrote
  that "which of these branches is still alive", "master first" and "never fix
  directly on a maintenance branch" are policy rather than history. Git answered
  and the answer did not carry the policy.
- What a derived answer would have said is the wrong thing. The session nearly
  proposed cherry-picking into `BP_15_0` and below, which are not supported at
  all, and it names what stopped it: the Node version each branch pins in CI,
  not any knowledge of the support policy. A `BP_*`-and-tags pattern reported by
  this server is the same inference with this server's confidence on it.
- Git state is already declared outside the boundary. It is the first
  `doesNotCover` entry in `knowledge/server-scope.json` — "Your git state:
  changed files, branch, working tree, commit message, history" — and
  `D-FBK-037` declined a git-reading tool for a reason that holds here too: git
  is local, documented, and answers the first time. `D-FBK-038` carries that
  half forward unchanged.
- The rule the session needed exists nowhere.
  `bin/cli hints:probe "release branch and backport policy for a project package"`
  returns three PHP hints about breaking changes, public API surface and a TER
  release, none of which is about it.
- Every occurrence of "backport" in `knowledge/` is the core's own process — the
  changelog directory a backport goes into, the Change-Id a backport on a
  release branch keeps, the browser baseline an older line carries. For a
  package that is not the core the word appears nowhere, and the core's rule is
  the one an agent carries in by default.
- No skill owns the phase where the cost was paid. `typo3-extension-cleanup` is
  the skill for carrying findings through to committed changes and names no
  branch, no push and no release line; the draft `typo3-extension-patch-review`
  stops at the verdict and routes there for the change itself.
- `skills/base.md` stops one step short in the paragraph that owns this. "What
  this server does not know" says which branch you are on is yours to establish;
  it does not say that which branch a fix belongs on is the repository's policy
  and derivable from nothing here.
- `D-ANS-085` is where the tool's boundary was drawn: what the repository's own
  files declare, wherever a project root is found. The commands, the patches and
  the DDEV lifecycle are all of that kind — declared, machine-readable, one
  right answer. A release policy is none of the three in the repository this
  feedback came from.
- The corpus is 8 open across 3 directories. Five are this checkout and all five
  are one session, so the two that name this ground — this one, and
  `feedback/2026-08-18-113425` naming the document it wanted, "Release branches
  and backporting in a project package" — are one session twice rather than two
  sessions.

## Decided

- Step 1a, the knowledge, and the statement missing is a negative one: that no
  source here carries a package's release policy. It is established from this
  repository rather than from TYPO3, so nothing is owed to `.checkouts/` or the
  manual.
- The half asking `typo3_project_describe` to report a policy inferred from
  branches and tags is declined. It is the inference the session made and was
  saved from by a coincidence, and a field carrying it would make the same
  mistake with this server's authority behind it.
- Queued rather than closed on the spot. A `SKILL.md` is installed into somebody
  else's project, where a wrong sentence is not corrected by the next release of
  this server, and `documentation/records/judging.rst` puts a contract beyond a
  run that has read only this repository.
- Priority `normal`, above the `low` a card arrives at. This is the half of the
  feedback that would have prevented the cost, the change is a rule on a surface
  that already exists, and nothing blocks it.
- The rule goes into `skills/typo3-extension-cleanup/SKILL.md`, at the step
  where a change is committed, because that is where the branch is chosen. Not
  into `skills/base.md`: it would cost every task a paragraph for a question
  only a task that lands something asks.
- Not a document. `D-FBK-043` answers a structure with one, and what is missing
  here is a statement rather than an order of steps — there is no TYPO3-wide
  convention for a package's release lines to write down.
- The feedback stays open until the commit that writes the rule archives it, and
  the card that served it carries the next step instead of the judgement it was
  asking for.

## Assumed

- That a package's supported lines are not reliably declared in a file this
  server already reads. What exists is narrower: a Composer `branch-alias` maps
  a development branch to a version, and a workflow's branch triggers say what
  CI runs on. Neither says which lines are still supported, and no repository
  was read to check how often either is present.
- That the session landing a change is the one that needs the rule. The cost was
  paid at the push and the pull request, both of which are inside that task.
- That one session reporting this twice is one report. The judgement is made on
  what the session had to do instead, which the feedback states, rather than on
  a count.

## Wrong if

- A session reads a release policy out of a file this server already opens — a
  `CONTRIBUTING.md`, a `.github` config — and pays for the reading. Then the
  declined half was real, and what was wrong was inferring it from refs rather
  than reporting what a file declares.
- The rule arrives and the session pushes to a maintenance branch anyway. Then
  it is step 4, wording, and a rule stated where the task passes was not enough.
- A session gets the rule, asks, and the maintainer answers that it is written
  down in the repository. Then the asking is a step this server made necessary.
- A task that lands a change in a package turns out not to run under
  `typo3-extension-cleanup`. Then the placement is wrong and the rule reaches
  nobody, which is step 2 arrived at in advance.
