# Writing a requirement

What a requirement **is** and where it lives is
[requirements/readme.md](../../requirements/readme.md). This is how one is
written.

An entry is added when a feedback is worked off, not when it arrives: a feedback
nobody has judged yet is a feedback, not a requirement. What the entry holds is
only what must be true — the assumptions and the evidence behind the change are
[decisions/](../../decisions/readme.md), and how one of those is written is
[writing-a-decision.md](writing-a-decision.md).

## What an entry looks like

```markdown
---
id: R-DIS-009
status: held
---

# R-DIS-009 — A negative is never remembered

**Nothing that says "there is no installation" is remembered.**

A successful resolution is memoized for the process; a failure is retried on
every call, because the caller who reads that answer is the one likely to
install, migrate or start something and ask again in the same session.

## From

A session lost to a cached negative — the agent ran `composer install`, started
DDEV, verified `bin/typo3` answered, and every tool kept reporting no
installation until the client was restarted (2026-07-29).

## Held by

- `InstanceTest::anInstallationThatAppearsDuringTheSessionIsFound`
```

- The **bold first sentence** is the requirement. Everything under it is why it
  is one, and a reader who stops after the bold line has read the whole demand.
- **From** is the session, review or feedback the demand came out of, with its
  date. It is evidence, not decoration: it is what tells the next person
  whether the requirement still describes a real failure.
- **Held by** lists the tests that hold it, one per line, or says in as many
  words that something is not guarded. A whole test class is a legitimate item
  — `VersionsTest` in full is a claim about every method in it, and naming them
  one at a time would go stale on the next one written. A test named there has
  to exist; a requirement claiming a test that was renamed away is a claim
  nobody answers for.

## What the state means

`status` is `held` or `open`, and a third state is derived from them:

- **open** — accepted and not built yet. That is the backlog, and it is
  deliberately the same list, because a requirement nobody has implemented and
  one that could silently regress are the same kind of thing.
- **not guarded** — built, and **Held by** names no test. It is never written
  in the front matter: an entry may not claim it of itself, and it is what a
  claim of `held` turns out to be. It is the honest answer for a requirement no
  test can hold, and it is the one worth seeing in a listing, because from afar
  it looks exactly like `held`.
- **held** — built, and the tests it names hold it there.

They are the `RequirementState` enum. `bin/cli requirements:check` cannot fail
on **open** or **not guarded**; both are legitimate, and `bin/cli backlog:list`
reads them out instead, together with whether a todo in
[todo/](../../todo/readme.md) names the id.

## What it rests on

A requirement may name the decisions it stands on in its front matter:

```markdown
---
id: R-FBK-007
status: held
restsOn: [D-FBK-005]
---
```

`bin/cli requirements:check` fails on an id no decision has. Whether a decision
it rests on was later revoked is a reading rather than a failure, and
`bin/cli backlog:list` is where it is read out — a revoked decision leaves the
requirement's test green and the reasoning under it gone, which nothing else
would say.

`bin/cli requirements:check` holds every file to the shape above, and
`composer test` runs the same check through `RequirementsTest`.
