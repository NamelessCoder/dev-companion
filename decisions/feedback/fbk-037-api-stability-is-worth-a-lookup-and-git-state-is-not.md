---
id: D-FBK-037
date: 2026-08-03
status: open
---

# D-FBK-037 — API stability is worth a lookup and git state is not

**The API-stability question is taken on and the git-state question is declined,
because one costs the caller a reading it cannot finish and the other costs it
one command.**

Two tool-gap reports arrived from the same patch-review session and read alike.
Judged against `D-FBK-027` they come apart.

## Evidence

- The API-stability report had to read `GifBuilder.php` **and its history** to
  establish that the class is public API while the removed
  `getTemporaryImageWithText()` is `@internal`. That is the fact deciding
  whether a removal is breaking, and the reading does not end in one call: the
  annotation may sit on the class, on the member, or on neither, and the report
  asks for the answer on each covered major, which a caller's own checkout
  cannot give because it has one.
- The git-state report had to retrieve the changed paths and the commit message
  with repeated path-filtered `git show` calls. Both facts come out of one:
  `git show --name-only --format=%B HEAD` prints the message and the paths
  together. The cost reported was the shape of the calls made, not an access
  path that has to be discovered.
- The Forge case `D-FBK-027` was decided on is the contrast. There the caller
  met 403, then 200 wrapping a challenge page, then JSON whose answer sits in a
  field nobody would guess. Git has no such trap: it is local, documented, and
  answers the first time.
- The mechanism for the first is already in this package. `PhpArray`,
  `Extension` and `FluidNamespaces` read shipped PHP with `token_get_all`
  without executing it, which is the same token stream a docblock sits in.

## Decided

- The API-stability lookup is queued as a card of its own. What it is not yet is
  designed: where the per-version answer comes from is the open question, and
  the card carries it rather than assuming the installation.
- No git-reading tool. It would replace one command the caller already runs with
  one call to this server, which is the definition `D-FBK-027` gives for what
  does not qualify — a fact the caller reads once from its own checkout.
- What the second report was actually right about is fixed instead. The scope
  entry told the caller to determine the changed paths themselves without saying
  how, while two tools here demand exactly that as input; it now names the
  single command that produces both.
- The feedback behind the second is answered and archived. The first stays open
  behind its card.
- Running git was not rejected as out of bounds. `Typo3Cli` already shells out
  to the installation's console, and a read-only `git show` is no different in
  kind. It is rejected on the count, which is the argument `D-FBK-027` asks for.

## Assumed

- That a class's `@internal` moves rarely between majors, so the per-version
  half of the report is a smaller promise than it reads as. Nobody has counted
  it, and the card asks for that count before the source is chosen.
- That the caller has git. Every checkout this server is pointed at is one, and
  the reports both came from sessions already running git commands.

## Wrong if

- A session asks for the changed paths in a way this server could have answered
  and pays more than the one command for them — a shallow clone, a detached
  worktree, a review of somebody else's patch that is not checked out at all.
  The last one is the plausible case and would reopen it.
- The API-stability lookup, once built, answers `unavailable` more often than it
  answers, which would mean the source question the card carries was settled the
  wrong way.

## Covered by

- `ScopeTest::noExclusionDeniesASourceTheServerReads`
