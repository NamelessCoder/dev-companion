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
answer has changed since the last session. Checked 2026-08-01: still v0.7.0,
nothing to do.

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

## Name an `E-EXT`, then run REVIEW-02 in it

This is first because everything below it waits on the same missing thing, and
because what it would test has only ever been seen in one checkout.

`REVIEW-01` reached `covered` on 2026-08-01 after four recorded runs, and what
got it there is in `decisions.md`: the initialize instructions name an entry
point, the conformance description leads with the open review, `skills/base.md`
fixes the order every task starts in, and two tool defects were repaired. All of
that is proven in exactly one environment, by one skill, against one site
package. `REVIEW-02` is the first run that would show whether the base holds when
the checkout is an extension repository rather than a project — and whether the
three skills whose order was corrected without ever being run forward behave
like the one that was.

The blocker is the environment, not the work: **no `E-EXT` is named anywhere.**
`scenarios/readme.md` describes it as a standalone extension repository with its
dependencies installed, TYPO3 among them; which checkout on this machine plays
that role has never been decided. Decide it, write it here the way `E-SITE` is
written below, install its dependencies, then
`bin/scenarios record REVIEW-02 claude-code`, paste what `bin/scenarios show
REVIEW-02` prints into a fresh client session in that directory and nothing
else, and fill in the judgments from the client transcript.

Prefer a repository that has a TYPO3 major to go up, because that makes the same
run serve the upgrade item below. If none is available, run it in one that does
not and record the upgrade item as still unblocked.

`E-SITE` is `/home/benji/projects/site-new`; its site package is below
`extensions/printworks_sitepackage`, TYPO3 14.3.5 under DDEV. It carries an
existing modification to the project's `.gitignore` that belongs to another
session and must be preserved. A forward run is a fresh MCP client session with
the installed skills — refresh them first with
`ddev exec php vendor/bin/typo3-cms-mcp update --agent=claude` — and a session in
this repository may neither activate those skills nor grade its own
implementation as behavioral evidence.

## Complete the extension author's multi-major upgrade workflow

This serves
`feedback/2026-07-30-173821-extension-upgrades-need-a-task-owned-workflow.md`
and `EXT-01`, and it waits on the item above for its environment. Run `REVIEW-02`
in an extension repository that has a major to go up, reduce the note to what the
run demonstrates, and read `bin/scenarios contract EXT-01` for the routing that
has to survive. Then add a thin `typo3-extension-upgrade` skill that orders
installed changelog and scanner/deprecation evidence, official versioned
documentation, shared-versus-version-specific implementation decisions, and a
Composer-resolved test matrix — it starts from `skills/base.md` like every other
skill and states only what it adds, so project and extension scope are not
restated in it. Keep concrete version facts out of the skill, publish it through
`Installer`, add its forward acceptance result, and add the requirement and tests
that hold only the behavior the run proves.

## Add the static-quality branch to `typo3-extension-testing`

This serves
`feedback/2026-07-30-174423-extension-static-quality-needs-an-explicit-workflow.md`
and `R-SKL-2`. It sits behind the two items above because the note asks for a
run it cannot have yet. `REVIEW-02` is the run: static quality is one of the
concerns its criteria admit, so an extension whose PHPStan and code-style
infrastructure is incomplete is the environment to run it in — and whether the
review raises that on its own is part of the result. Reduce the note to what it
demonstrates. Then add an on-demand static-quality reference to
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
that starts from `skills/base.md` like every other skill, selects the intended
registries and version, and ends preparation with the artifact path, its
checksum, what the
archive includes and excludes, the verification results, the open blockers and
the publication steps deliberately not taken. Registry requirements come from
current official documentation rather than from the skill, and the artifact is
built through the repository's own release command where one exists. Tagging,
pushing and registry publication stay a separate phase that needs an explicit
request and a confirmed repository, version and credentials. What has to fail is
an artifact carrying development files or secrets while the checkout itself is
green; if that shape survives the work, it earns a contract case of its own.

## State the skill authoring contract in one place

This serves what is left of
`feedback/2026-07-30-173821-task-skill-forward-evidence-is-not-repeatable.md`
after it was trimmed on 2026-08-01. Its runner half is done and `skills/base.md`
now holds the order a task runs in; what stays distributed is how a skill is
*written* — body procedural, versioned facts left in their owning tools,
references one hop away and loaded on demand, ownership and failure boundaries
stated, a realistic scenario before a new domain becomes a skill.

Several of those are already assertions in `SkillTest` that each skill restates
in its own words. The step is to give them one written form the way the evidence
order has one, and to read the assertions from that instead of from five copies.
It sits last because it pays off when the next skill is written, and the three
items above are what would write one.

## Not queued, and deliberately so

Things a session may otherwise rediscover and mistake for work:

- **`REVIEW-03`** needs a core checkout with actual uncommitted changes.
  `/home/benji/projects/typo3-cms` is on `main` with a clean tree, so the review
  has nothing to read. It needs a patch in progress before it can run at all.
- **The catalog roadmap** — an API signature lookup, a changelog scaffold, a test
  scaffold, and the structured-output envelope that needs a spike of
  `vendor/mcp/sdk` first. None of it is blocked; none of it serves an open note
  or a forward review either, which is why it is below everything that does.
- **`phpstan/phpstan` 2.2.6 → 2.2.7 and `phpunit` 11.5 → 12.5.** Ordinary
  maintenance, not an item.
