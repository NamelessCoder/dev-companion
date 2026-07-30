# What is being worked on

This file exists so a session can end anywhere. It holds the **order of the
work** and where the last one stopped — not what must be true (that is
[requirements.md](requirements.md)), not the questions real sessions asked (that
is `feedback/`), not the map of what the audiences need (that is `scenarios/`).
Those three outlive the work; this one is consumed by it.

Rules that keep it from becoming a fourth backlog:

- An item names what it serves — a requirement, a note, a scenario. An item that
  serves nothing is not a task, it is an idea, and ideas go in the note that had
  them.
- An item says what the **next concrete step** is, in enough detail that someone
  who has read nothing else can start. "Continue with the bindings" is not that;
  "bind the statements in `php.json` against `.checkouts/12.4` and `13.4`" is.
- A finished item is deleted, not ticked. What it established is already in
  `requirements.md`, and the commit is the record that it happened.
- The order is the order. When something jumps the queue, it moves up here
  first, so the reason is written down before the work starts.

---

## Every session starts here: read `feedback/` again

This item is never done and is never deleted. Notes arrive while work is
happening — a session somewhere records what it was missing, and the file lands
in `feedback/` without anyone being told. And notes that were open yesterday are
often half answered by what shipped since, without their text saying so.

So, before picking anything up:

1. `typo3_feedback_list` — or read `feedback/` — for what is there now.
2. For each note, **run its own query against the current server**. A note is
   evidence about a version of this server that may no longer exist.
3. Close what is answered, trim what is half answered down to the part that is
   still open, and let a new note that changes the order move the items below.

The notes are the only input that comes from outside this repository. Everything
else here was written by someone who already knew what they meant.

---

## Standing check: has `mcp/sdk` released a newer version?

This item is never done and is never deleted either, and unlike everything else
here it serves no single requirement. That is the point: it serves the
precondition of all of them. Every answer this server gives travels over the
protocol version the SDK speaks, and on the day a client stops offering that
version, every requirement fails at once and not one of them says so. So the
check is cheap and it is standing — one command, and only actionable when the
answer has changed since the last session:

    composer outdated mcp/sdk

`mcp/sdk` is the official https://github.com/modelcontextprotocol/php-sdk. Two
different things can come back, and they are worth different amounts:

1. **A patch or minor release.** Take it. The one waiting as of 2026-07-30 is
   v0.7.1, unreleased but sitting on `main` since 27.07., and what it carries is
   stdio hardening — memory exhaustion on `fgets`, a cap on the stdout buffer.
   `bin/typo3-cms-mcp` runs `StdioTransport` and nothing else, so that release is
   worth having on its own, with no protocol reason attached.
2. **A release that speaks `2026-07-28`.** Not there yet: v0.7.0 is the newest
   release and its `ProtocolVersion` enum stops at `2025-11-25`. PR #403
   («Implement protocol version negotiation») is the vehicle to watch — it names
   the 2026-07-28 revision and splits a «handshake era» from a «modern era» that
   has no `initialize` at all. When it lands in a release, the bump here is two
   places: the constraint in `composer.json` and `PROTOCOL_VERSION` in
   `tests/Smoke/StdioServerTest.php`.

What the 2026-07-28 spec asks of servers has been read once already, so that the
next session does not read it again: almost none of it reaches this one. The
stateless core, the `Mcp-Method`/`Mcp-Name` routing headers, the authorization
hardening and the deprecation of HTTP+SSE are all HTTP concerns, and this server
has one transport and it is stdio. Roots, sampling and logging are deprecated and
were never used. Multi round-trip requests are for a server that has to ask
something back, and these tools are read-only and answer in one turn. One new
thing would be worth having: cacheable list results, `ttlMs` and `cacheScope` on
a `tools/list` that only changes when this repository releases.

The deprecation windows are twelve months, so the outer edge is around July 2027.
Before then the only thing that has to happen is a version bump behind a release
somebody else writes.

---

## Close what the first EXT-04 run exposed

This serves the seven notes recorded on 2026-07-30, all from one forward run of
`EXT-04` against the Printworks sitepackage on TYPO3 14.3.5. They are worked off
in this order, cheapest and most certain first:

1. Deriving the component catalog from the installation, last, because it
   reverses a recorded decision. It needs a `decisions.md` entry that says what
   changed, not only a commit.

## Run EXT-04, SITE-07 and SKILL-07 forward and correct what they claim

This serves
`feedback/2026-07-30-173821-task-skill-forward-evidence-is-not-repeatable.md`
and
`feedback/2026-07-30-185543-a-task-skill-keeps-documentation-work-that.md`,
`R-FBK-3` and `R-SKL-3`. The format, the runner and the authoring invariants
exist; what is left of those notes is the evidence itself.

`EXT-04` was run once, on 2026-07-30, and that run is void. It met all five
criteria of the day and produced six defects none of them measured, so the
criteria were widened and the digest dropped the run with them. It now waits
behind the item above, decided on 2026-07-30: the new criteria are exactly the
gaps those notes describe, so a run today fails the documentation criterion by
construction and reproduces the label and configuration defects — it would
confirm what is already written down, at the price of a full session. `SITE-07`
depends on none of it and can go at any time.

`SKILL-07` is the hand-off case: it must activate the backend-module workflow
first and the documentation workflow before documentation is edited.

Next step for each: `bin/scenarios record <id> claude-code`, paste what
`bin/scenarios show <id>` prints into a session in the Printworks project and
nothing else, fill in the judgments with their evidence, and correct the
`Status today` lines to what the runs establish.

## Complete the extension author's multi-major upgrade workflow

This serves
`feedback/2026-07-30-173821-extension-upgrades-need-a-task-owned-workflow.md`
and `EXT-01`. Run `EXT-01` verbatim in its `E-EXT` environment against the
current server and reduce the note to the demonstrated gaps. Then add a thin
`typo3-extension-upgrade` skill that orders project and extension scope,
installed changelog and scanner/deprecation evidence, official versioned
documentation, shared-versus-version-specific implementation decisions, and a
Composer-resolved test matrix. Keep concrete version facts out of the skill,
publish it through `Installer`, add its forward acceptance result, and add the
requirement and tests that hold only the behavior the run proves.

## Add the static-quality branch to `typo3-extension-testing`

This serves
`feedback/2026-07-30-174423-extension-static-quality-needs-an-explicit-workflow.md`
and `R-SKL-2`. It sits behind the two items above because the note asks for a
run it cannot have yet: no scenario covers static quality, and the runner that
would record one is the first item here. So write the missing forward scenario
first — a reusable extension whose PHPStan and code-style infrastructure is
incomplete — run its prompt verbatim in `E-EXT`, and reduce the note to the
demonstrated gaps. Then add an on-demand static-quality reference to
`typo3-extension-testing`: inspect the existing packages, configuration,
baselines, scripts and CI before changing any of them; resolve development
dependencies from the extension's declared TYPO3 and PHP range; establish one
project-owned command per check and keep check and fix modes apart; let CI call
the commands that passed locally. New errors are fixed rather than added to a
baseline, and automatic formatting stays inside intended first-party files.
Split it into a skill of its own only if the run shows that
`typo3-extension-testing` does not activate or ends up owning two unrelated
workflows.

## Carry an extension from release candidate to verified artifact

This serves
`feedback/2026-07-30-174423-extension-releases-need-a-preparation-and-publication-workflow.md`.
It is last because it is the largest of the four and composes the conformance,
testing and documentation workflows the three items above still change. Run the
note's query verbatim in `E-EXT`, then add a `typo3-extension-release` skill
that starts from project and extension scope, selects the intended registries
and version, and ends preparation with the artifact path, its checksum, what the
archive includes and excludes, the verification results, the open blockers and
the publication steps deliberately not taken. Registry requirements come from
current official documentation rather than from the skill, and the artifact is
built through the repository's own release command where one exists. Tagging,
pushing and registry publication stay a separate phase that needs an explicit
request and a confirmed repository, version and credentials. The forward
scenario has to fail on an artifact carrying development files or secrets while
the checkout itself is green.
