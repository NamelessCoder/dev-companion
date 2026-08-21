---
id: D-GUI-010
date: 2026-08-04
status: open
---

# D-GUI-010 — The commit workflow defaults to the repository most callers are in

**`typo3_commit_message_guide` defaults to `project`, and a patch against the
TYPO3 core states `workflow="core"`.**

`D-GUI-002` defaulted to `core` so that dropping rules would be something a
caller asked for. Its own **Wrong if** named what that costs everywhere else,
and the measurement it records is that the cost is the ordinary answer there.

## Evidence

- The cost was measured rather than feared. Called with `changeType` and
  `summary` and no `workflow`, the guide answers with `Resolves: #ISSUE_NUMBER`,
  `Releases: RELEASE_TARGET` and the hard `missing-issue` error — re-measured
  over stdio on 2026-08-04 and recorded on `D-GUI-002`. In a repository with no
  Forge issue behind it, that draft is not one anybody can commit.
- Three audiences read this server and one of them writes core patches. The
  knowledge base is written for core contributors, extension authors and site
  developers alike, and only the first of those has a Forge issue and a release
  target.
- This repository is its own witness. `AGENTS.md` writes by the project rules
  and says so, naming `workflow="project"` as where its two widths come from —
  `D-DOC-013`.
- Every call site that means project already states it: five published skills,
  the extension task intent, and the outside-the-core branch of
  `typo3_task_guide`. What the default decides is only the calls nobody wrote,
  and those are the ones a session makes on its own.

## Decided

- The default is `project`: the keyword, the 52/72 widths and the wrapping, with
  no trailer added or demanded.
- Core is stated rather than assumed. The two core skills, the core task intents
  and the core branch of `typo3_task_guide` name `workflow="core"`, and a core
  checkout is where a caller is most likely to be reading one of them.
- `[SECURITY]` follows the workflow it already followed: refused where the
  caller says `core`, accepted otherwise, because outside the core nobody holds
  the Security Team's reservation. What changes is that the reservation is now
  opt-in.
- The inference `D-GUI-002` refused stays refused. The workflow is still an
  argument and still not read out of the subject text — only which of the two it
  falls back to has moved.

## Assumed

- A core contributor reaches the guide through a route that names the argument.
  The two core skills and the core intents do; a session that calls the tool
  cold does not, and there the failure is quieter than the one it replaces.

## Wrong if

- A core patch is committed with a message the guide drafted and neither
  `Resolves:` nor `Releases:` in it, because nothing in the answer said one was
  missing.
- A contributor gets `[SECURITY]` accepted for a core patch by leaving the
  argument out.
- The routes that are supposed to state `core` stop stating it, so the default
  decides a case it was never meant to.

**Since then**, on 2026-08-04, a session measured what the project workflow is
worth over twelve commits. `feedback/2026-08-04-180133` called
`typo3_commit_message_guide` once, for the first commit, and hand-wrote the
other eleven: the answer was correct — a wrapped draft and a warning that the
summary ran to 68 characters against the preferred 52 — and once the shape of an
accepted message was in the session, the round trip stopped paying.

That is the default working rather than failing. The core workflow carries the
Forge issue and the Releases trailer, which cannot be recalled from the last
commit; the project workflow carries two widths and a wrap, which can. Nothing
is changed for it, and it is written here because a usage curve that drops after
the first call reads as a tool nobody wants when it is a tool that taught.

On 2026-08-18 a session that never called the guide reported the same default
from the other side. `feedback/2026-08-18-113357` wrote six commit messages in a
sitepackage repository by hand, read the schema at the debrief, and says nothing
about the behaviour needs changing because the default was already the case it
was in. What the **Assumed** above says of a core contributor holds for a
project one too — the route has to name the tool — and under a client that
defers tool schemas the name was all this session had. That half is judged on
`D-AUD-011`.

**On 2026-08-21** a third session was judged here, and it is the first to report
the default from inside the audience it was chosen for.
`feedback/2026-08-19-090253` wrote a fourteen-line `[TASK]` message in an
extension repository without calling the guide, having read the routing line
that names exactly that case. It lost on confidence rather than on placement,
which is the curve the paragraph above already records: a keyword, two widths
and a wrap are recallable once seen.

What the session could not have recalled is the footer, and the schema told it
there was none. `issue` and `relatedIssues` are described as Forge issue
numbers, and the tool's own description says the Forge issue does not apply
outside the core, while `CommitMessage::create()` writes `Resolves:` and
`Related:` from both in either workflow. Re-measured on 2026-08-21: a `project`
call passing `issue` returns a draft carrying `Resolves: #348` above the closing
line saying the Forge issue does not apply. That is ladder step 4, and the
wording disagrees with the answer rather than merely reading awkwardly.

The behaviour is the half `R-AUD-003` leaves unstated. That requirement holds
the guide to adding no trailer that means nothing outside the core, and says
nothing about the one a caller passed on purpose. Which form an extension
repository wants is a reading rather than a recall, so the repair is queued on
the card serving that feedback rather than made here. That card was worked the
same day: the form is `Resolves:` outside the core too, so what changed is the
wording alone, and `D-GUI-017` carries what the reading found.

The same feedback asked which keyword a dependency update takes that also
migrates an API and regenerates committed build artefacts. Nothing is built for
it. `[TASK]` is what a change that is neither a bug fix nor a feature already
is, the session chose it correctly from the four the corpus lists, and one
session wanting confirmation of an answer it got right is not evidence that a
rule is missing.
