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

## Why the four items below come before the twin hints

Written down before the work starts, because it is a change of order. The twin
hint item served the one open note and was next; a measurement on 2026-07-30 put
four items in front of it, and the reason is that they are its precondition
rather than a better use of the time.

`ArchitectureHints::scoreHint` scores a query against `appliesTo` and against
nothing else — not the title, not the hint text. That is 57 entries and 11,501
words of hint body reachable through an average of 9.3 hand-written keywords
each. Eighteen realistic queries were put through the matcher and seven of them
reached nothing at all, among them `my extension service is not found at
runtime` and `page title provider does not work` — both of which are the
`dependency-injection-services` hint, which says «The failure is a
service-not-found at request time» in so many words. A twin hint written into
that corpus inherits the same problem, so it is worth writing after the corpus
can be found in, not before.

---

## Make the hint text searchable, not only its keywords

Serves `R-KNW-2`, and revises what that requirement assumed. R-KNW-2 accepted
`appliesTo` as the thing the matcher scores and made curation the answer: carry
the words the subject is asked about. The measurement above is what that costs
at 57 entries — every phrasing has to have been thought of in advance, and the
index `R-ANS-6` returns on a miss is the mitigation that made it survivable
rather than the fix.

The next concrete step is in `ArchitectureHints::scoreHint`: score title and
hint text alongside `appliesTo` with the weighted term scorer `Knowledge`
already has (`Knowledge::weights`/`scoreSection`), keeping `appliesTo` weighted
above body text so the curation still decides where it exists. Two of the four
matchers in `src/` become one that way.

What holds it: the eighteen queries are the test. They belong in `HintsTest` as
a table of query and the id that has to come back, with the seven that reach
nothing today named explicitly — `my extension service is not found at
runtime` → `dependency-injection-services`, `file upload storage configuration`
→ `file-abstraction-layer`, `my button looks wrong` → the CSS components,
`validate a form field in the backend` → `tca-formengine`. Add the one that
matched the wrong hint too: `dark mode colors in my backend module` returns
`backend-modules` and has to also reach `css-light-dark-mode`.

---

## Dissolve the architecture prose into the hint corpus

Serves `R-ANS-7` and the version binding, and depends on the item above.

`knowledge/typo3-core-architecture.md` has 14 `##` sections and every one of
them is a hint by the same name; `knowledge/typo3-css-architecture.md` has 21
and 19 of them are. The prose copies are the older ones — its «Dependency
Injection and Services» is the hint minus the page title provider trap, which is
the one statement in it that fails silently at runtime. And markdown carries
neither `since`/`until` nor `binding`, so both documents are the one corner of
the knowledge base where the two mechanisms this repository spent its last work
on cannot reach.

They are also reachable unfiltered three ways: `typo3_rule_lookup` searches
every document, `typo3_task_guide` does the same, and `typo3://core/{id}` serves
any of them raw.

The fallback in `ArchitectureHints::find` was the argument for keeping them —
prose as a second matcher where no hint scored. The measurement says it is not
one: of the seven misses it rescues one and answers four with noise, and `file
upload storage configuration` comes back as «Language Files, Fluid Templates,
Testing Strategy, Documentation and Changelog».

The next concrete step is one pass, section by section, over both documents:
diff each against the hint of the same name, move what the hint does not have
into it as statements — bound where it is bound — and delete the document when
its sections are empty. Four sections have no hint counterpart and are the ones
that need a decision rather than a move: «Core Principles» and «Definition of
Done» in the CSS document, and the Sass bundle taxonomy under «Component
Structure». Then remove the `knowledgeSections` fallback, its field in the tool
result, and the CSS-document special case beside it.

Left standing as prose: `typo3-core-rules`, `typo3-gerrit-workflow`,
`typo3-commit-messages`, `typo3-contribution-sources`, `typo3-core-scripts` —
narrative with no per-statement obligation on it.

---

## Take the version number out of the rules prose

Serves the version binding, and is independent of everything above — it is a
wrong answer being given today, and it is two lines.

`knowledge/typo3-core-rules.md:42` reads «Since TYPO3 v14.1 a label marked that
way raises an `E_USER_DEPRECATED` error». `typo3_rule_lookup` has no
`targetVersion` and searches every document, so a caller on 13.4 is handed it
unqualified. AGENTS.md forbids exactly this shape and `HintsTest` does not see
it, because it reads JSON.

The next concrete step: move the XLIFF label lifecycle statements out of
`typo3-core-rules.md` into the `language-files` hint, where the version is a
field and the filter applies. Most of that section is the core's own — the
`x-unused-since` marker, migrating core usages in the same patch, the changelog
entry — so it carries `binding: "core"` rather than being dropped; the two
`runTests.sh` invocations at the end are `checks`, not statements. What is left
to bind is the sentence itself: verify the label deprecation on both sides,
against `.checkouts/13.4` and `.checkouts/14`, and name both branches in the
commit message.

---

## `bin/hints` — make an unreachable hint loud

Serves the two items above by making their effect measurable, and every hint
written after them. `bin/verify-catalog` exists because a core update
invalidates the catalog silently; a hint nobody can phrase their way to decays
the same way and nothing says so.

Same shape as `verify-catalog`: a script in `bin/`, no new dependency, runnable
in CI.

- `bin/hints probe "<query>"` — what that query reaches, with the score and
  which field earned it. The eighteen-query table becomes a command instead of a
  throwaway script.
- `bin/hints coverage` — which hints no prompt in `scenarios/` reaches, and
  which scenario prompts reach nothing. Neither number is known today.
- `bin/hints lint` — statements carrying a version number in their text without
  a binding, and hints whose `appliesTo` is fully contained in another's.

---

## One name for who is obliged, and a binding where there is none

Serves `R-AUD-5`, which describes the mechanism as one and finds it spelled
three ways: `binding: "core"` on hints, `coreOnly: true` in
`knowledge/task-intents.json`, `provenance: "core-only"` in
`knowledge/server-scope.json`. Three field names, three readers, one axis.

Nothing is wrong today, which is why this is last — it is the shape going wrong
the next time somebody adds a fourth.

The next concrete step is one name across the three files and the readers that
consume them, with `VersionsTest::whoIsObligedIsWrittenAsDataToo` extended to
hold all three rather than only the hints. In the same pass: `task-intents.json`
has no version binding on 9 entries whose checklists name changelog paths and
extension scanner rules, and `test-suite-hints.json` has none on 24 suites that
come and go between majors. Give both `since`/`until` and filter them by
`targetVersion` the way the hints are filtered.

---

## Go through what is marked `binding: "core"` and say what the project side is

Serves the one note left open,
`feedback/2026-07-29-180528-project-work-needs-a-second-axis-the-repository.md`.
The count that item used to ask for has been made: differing answers already
existed in four shapes, and what was missing was the force of a statement, which
is now `binding: "core"` on 22 hints and 4 single statements.

Marking says "this is not yours to follow". It does not say what is, and that is
what a twin hint is for. The next concrete step is one pass over the marked
entries — `grep -l '"binding": "core"' knowledge/architecture-hints/*.json`, then
the four statement-level ones — and per subject one of two answers, written into
the note: marking is enough (the backend CSS rules are wanted unchanged by a
project that builds a backend module, and a twin would say the same thing
twice), or the project side is a real answer that is missing.
`documentation-changelog` is the clearest candidate for the second: a project
writes release notes too, and the hint says nothing about that. Where the answer
is the second, the twin is written the way `project-extension-tests` was — same
subject, its own hint, and each pointing at the other.

Below that, nothing is queued: everything else written down so far is in
`requirements.md`, so the work after it is whatever the notes a session finds
ask for — or, where there are none, a scenario from `scenarios/` still marked
`gap`.
