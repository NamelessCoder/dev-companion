# What is being worked on

This file exists so a session can end anywhere. It holds the **order of the
work** and where the last one stopped — not what must be true (that is
[requirements/](requirements/readme.md)), not the questions real sessions asked (that
is `feedback/`), not the map of what the audiences need (that is `scenarios/`).
Those three outlive the work; this one is consumed by it.

Every section opens with one line that says which of three things it is, so a
reader that has read nothing else can tell the queue from what surrounds it —
and so `bin/cli next` can read it out:

| Line | The section is |
| --- | --- |
| `**Standing:** notes` / `backlog` / `by hand` | never deleted, and read before anything is picked up. The first two are what `bin/cli next` runs for you; `by hand` prints the commands the section names. |
| `**Serves:** <ids>` | an item, in the order the queue has it, answering for the requirements, notes and scenarios it names. |
| `**Not an item.**` | neither — something a session would otherwise rediscover and mistake for work. |

Rules that keep it from becoming a fourth backlog:

- An item names what it serves — a requirement, a note, a scenario — on that
  line, where it can be read. An item that serves nothing is not a task, it is
  an idea, and ideas go in the note that had them.
- An item says what the **next concrete step** is, in enough detail that someone
  who has read nothing else can start. "Continue with the bindings" is not that;
  "bind the statements in `php.json` against `.checkouts/12.4` and `13.4`" is.
- A finished item is deleted, not ticked. What it established is already in
  `requirements/`, and the commit is the record that it happened.
- The order is the order. When something jumps the queue, it moves up here
  first, so the reason is written down before the work starts.

---

## Every session starts here: read `feedback/` again

**Standing:** notes

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

## And then reads the backlog

**Standing:** backlog

Standing as well, and never deleted. `feedback/` is what arrives from outside;
this is what was written down inside and then waited.

    bin/cli backlog list

Three states mean unfinished, and none of them can make a check fail: a
requirement marked **open**, one held by `not guarded`, and a decision still
`standing`, whose **Wrong if** nobody has been back to. All three are
legitimate — which is why nothing read them for as long as they existed, and why
a requirement could sit there unbuilt from the day the directory was created
without a single session being told.

The listing says, per requirement, whether an item below names its id. An entry
nothing names is not a task somebody deferred; it is one nobody has looked at.
What is owed is therefore the judgement, not the work: either an item below that
names it, or the sentence in `decisions/` that says why it stays as it is. A
requirement no test can hold is a legitimate answer. A requirement nobody has
judged is not.

---

## Standing check: has `mcp/sdk` released a newer version?

**Standing:** by hand

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

## Which checkout plays which environment

**Not an item.**

The standing answer to a question every run below asks first. A
scenario names a kind of directory; which one on this machine plays it belongs
here, where it can go stale without taking a case with it. A forward run is a
fresh MCP client session with the installed skills, and a session in this
repository may neither activate those skills nor grade its own implementation as
behavioral evidence.

- **`E-SITE`** — `/home/benji/projects/site-new`, site package below
  `extensions/printworks_sitepackage`, TYPO3 14.3.5 under DDEV. The server is a
  dependency there: refresh the skills with `ddev exec php
  vendor/bin/typo3-cms-mcp update --agent=claude`. It carries a `.gitignore`
  modification belonging to another session; leave it.
- **`E-EXT`** — `/home/benji/projects/bootstrap_package`, TYPO3 14.3.0 below
  `.build/vendor`, DDEV project `bootstrap-package` on PHP 8.5. The server is
  **not** a dependency there, so it is reached from this checkout: `php
  /home/benji/projects/typo3-cms-mcp/bin/typo3-cms-mcp install --agent=claude`
  from the project root publishes the skills and writes the host-php `.mcp.json`.
  Repeat it after any skill change — the published skills are a copy and nothing
  reports it when they are older than the server. The generated ignore block in
  its `.gitignore` and the untracked `.mcp.json` are from that install and stay.
  `REVIEW-02` ran here twice on 2026-07-31, `partial` at 02:55 and `covered` at
  08:15 after the corrections that run earned.

`E-EXT` is a kind, and the two items below need it played by other checkouts —
one with a TYPO3 major in front of it, one without complete static quality
infrastructure. Whichever plays it for the next run is named here first.

## Add the static-quality branch to `typo3-extension-testing`

**Serves:** R-SKL-2, feedback/2026-07-30-174423-extension-static-quality-needs-an-explicit-workflow.md

It moved ahead of the upgrade item on 2026-07-31 for one reason:
it is the only one of the four whose next step can be taken today. The upgrade
item lost its environment when `E-EXT` turned out to have no major in front of
it, and this one gained half its evidence from the same run.

That half, as the second run of 2026-07-31 leaves it: with a complete PHPStan
and code-style setup in front of it, the review does not propose installing
tooling that is already there, and it no longer misreads the baseline — it says
in as many words that keeping those entries is correct while 13.4 is supported,
and asks only that the repository record what they are a work list for. What it
produces instead is three verification gaps in the infrastructure that exists:
unit and functional tests gated on PHP 8.5 while the declared floor is 8.2, no
`--prefer-lowest` leg so the low end of `^13.4 || ^14.3` is never installed, and
`failOnDeprecation` absent from both phpunit configurations, which is what would
otherwise surface a newly deprecated API on the next core minor. All three rest
on reading: the run again executed none of the ten commands the project
declares. That an existing setup is read rather than replaced is now evidenced;
what a missing one produces is not.

The other half needs a checkout where that infrastructure is missing, and
`/home/benji/projects/syntax` is one — php-cs-fixer and phplint, no PHPStan, no
tests, dependencies installed, TYPO3 14.3.0, DDEV. Name it as `E-EXT` above,
install the skills there the same way, run `REVIEW-02`'s prompt, record it, and
reduce the note to what the two runs together demonstrate. Then add an on-demand
static-quality reference to
`typo3-extension-testing`: inspect the existing packages, configuration,
baselines, scripts and CI before changing any of them; resolve development
dependencies from the extension's declared TYPO3 and PHP range; establish one
project-owned command per check and keep check and fix modes apart; let CI call
the commands that passed locally. New errors are fixed rather than added to a
baseline, and automatic formatting stays inside intended first-party files.
Split it into a skill of its own only if the run shows that
`typo3-extension-testing` does not activate or ends up owning two unrelated
workflows.

## Complete the extension author's multi-major upgrade workflow

**Serves:** EXT-01, feedback/2026-07-30-173821-extension-upgrades-need-a-task-owned-workflow.md

It has no environment: `E-EXT` supports `^13.4 || ^14.3`, so
there is no major in front of it, and no other extension checkout on this
machine is behind either. What it needs is one that is — an extension still
declaring `^12.4` or `^13.4` alone, cloned for the purpose if none turns up.
Name it above before starting, run `REVIEW-02`'s prompt there as a recorded run,
reduce the note to what that run demonstrates, and read `bin/cli scenarios contract
EXT-01` for the routing that has to survive. Then add a thin
`typo3-extension-upgrade` skill that orders
installed changelog and scanner/deprecation evidence, official versioned
documentation, shared-versus-version-specific implementation decisions, and a
Composer-resolved test matrix — it starts from `skills/base.md` like every other
skill and states only what it adds, so project and extension scope are not
restated in it. Keep concrete version facts out of the skill, publish it through
`Installer`, add its forward acceptance result, and add the requirement and tests
that hold only the behavior the run proves.

## Carry an extension from release candidate to verified artifact

**Serves:** feedback/2026-07-30-174423-extension-releases-need-a-preparation-and-publication-workflow.md

It is last of the four because it is the largest of them and composes the conformance,
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

**Serves:** feedback/2026-07-30-173821-task-skill-forward-evidence-is-not-repeatable.md

What is left of the note after it was trimmed on 2026-08-01: its runner half is
done and `skills/base.md`
now holds the order a task runs in; what stays distributed is how a skill is
*written* — body procedural, versioned facts left in their owning tools,
references one hop away and loaded on demand, ownership and failure boundaries
stated, a realistic scenario before a new domain becomes a skill.

Several of those are already assertions in `SkillTest` that each skill restates
in its own words. The step is to give them one written form the way the evidence
order has one, and to read the assertions from that instead of from five copies.
It sits last because it pays off when the next skill is written, and the three
items above are what would write one.

## Judge the two audience requirements nobody has judged

**Serves:** R-AUD-1, R-AUD-2

It sits here rather than further up for the reason that produced it: both have been waiting since `requirements/` was
created on 2026-07-29, no item ever named either of them, and what was urgent
about them was being invisible — not being late. The four items above serve open
notes and keep their order.

`R-AUD-2` is open and has a case already. `bin/cli scenarios contract META-03`
asks for two paths of different audience in one session and names the unmet half
outright: that the two stay apart is not guarded. The step is to read `Scope`
against that case and settle which of two things is true — the per-path decision
already exists and only the "the audience is uncertain" answer is missing, or
nothing combines the signals at all. The first is a wording change with a test
behind it; the second is a feature, and `D-SCO-6` already names the flag it
would rename (`outsideCore` → what it actually decides).

`R-AUD-1` is `not guarded` and may deserve to stay that way — a principle is not
a behaviour, and no test holds one. What it should not stay is untried:
`ScopeTest::whatTheScopeExcludesIsNotWhatTheServerAnswers` was written for
exactly this contradiction, and if it holds the operative half, the entry names
it instead of saying that nothing does. If it does not, the entry says in one
clause why nothing can, and that is the end of it.

## Sort the standing decisions by what could check them

**Serves:** decisions/

Twenty-nine of thirty-one entries are standing:
each one wrote down what would show it wrong, and nobody has been back to any of
them. Most are standing because they are still true, so reading all twenty-nine
for their own sake is not the task and would not finish.

The step is one pass that splits them three ways by their **Wrong if** alone:
one this repository can answer on its own (a checkout, a command, a test), one
that needs a forward run in an environment named above, and one that waits on
something outside this repository entirely. Only the first group is work, and it
becomes items. The pass is cheap — the field is the last one in every file — and
it is what turns a count into a queue. Nothing is checked while sorting.

## Not queued, and deliberately so

**Not an item.**

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
