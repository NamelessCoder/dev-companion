---
id: D-KNW-068
date: 2026-08-10
status: open
---

# D-KNW-068 — Looking at a backend change is a suite the core already carries

**`-s e2e-prepare` installs the instance, publishes it and waits, so the answer
to "look at this in a real browser" is a suite this server was not listing.**

A session reviewing a positional core patch had no procedure for looking at it,
verified in synthetic DOM it had built itself, and shipped three blind
corrections before the developer asked whether it had actually looked.

## Evidence

- `feedback/2026-08-10-101714`. What the session concluded from the checkout is
  half right: `-s e2e` does fix the Playwright command and passes nothing
  through. What it did not find is that the script has two more suites for
  exactly its case, and it built a Playwright harness of its own instead.
- `runPlaywright()` on `main`: with `PLAYWRIGHT_PREPARE_ONLY=1` the web
  container gets `-p 127.0.0.1::80`, and the script prints the instance URL, the
  local headless command and the local UI command, each with
  `PLAYWRIGHT_BASE_URL` already set, then waits — Enter re-runs the specs in the
  container, Control-C ends it. That is both halves of what the session was
  missing: a backend to open, and a way to run one spec, since the command it
  prints is one the caller composes.
- `e2e-prepare` is on `main`, `14.3` and `13.4` and not on `12.4`;
  `e2e-browser`, which serves Playwright's own UI at the port the help text
  names, is on `main` and `14.3` only. `PLAYWRIGHT_USE_EXISTING_INSTANCE`
  appears on the same three branches as `e2e-prepare` and skips the composer
  install of the test instance, which is the session's own measurement of what a
  rerun costs.
- `knowledge/test-suite-hints.json` carried `e2e` alone. So
  `typo3_test_run_guide` answered a positional backend change with a headless
  suite and nothing to look at, which is the answer the session acted on.

## Decided

- Both suites are declared, bound to the branches that have them, and their
  `whenToUse` says what the run prints rather than only what it tests. What
  makes them findable is the second sentence: a suite the caller reads as "run
  the tests" is not what it reaches for when it wants to see something.
- `browser-tests` gains one bound statement pointing at them, because the
  session asking how to look at a change asks a hint lookup, not a run guide. It
  says what the suite is for and leaves what it prints to the guide that owns
  it.
- `e2e-prepare` carries the CSS domain as well. A Sass change is one of the
  changes somebody has to look at, and verifying in one engine is what
  [`D-KNW-066`](knw-066-the-browser-baseline-is-a-release-day-and-core-usage-is-not-evidence-of-it.md)
  cost a session on the same day.
- The other half of the report — pointing a browser at the developer's own
  installation, where the data that shows the defect actually is — is
  [`D-KNW-069`](knw-069-a-browser-in-a-container-reaches-a-ddev-site-on-the-routers-own-network.md).
  The prepared instance is a styleguide with one language and no scrolling, so
  it could not have shown this bug either.

## Assumed

- That the printed commands work as the script writes them. They are read out of
  `runPlaywright()` and nothing here ran the suite; the container, the port
  publication and the two `npm run` scripts are all in the checkout.

## Wrong if

- A session reads the suite list, runs `-s e2e-prepare` and still cannot look at
  its change. Then the gap is the instance rather than the route to it, and the
  answer is what the open half is about.
- The two suites turn out to be noise in ordinary answers — a CSS or PHP task
  told to install an instance it did not need. Neither is `base`, so they are
  listed only where the query matched them; a report of the opposite would say
  the matching is too loose.

## Since then

2026-08-14. Two feedback from one session reviewing a Playwright-only change
land on this entry's `whenToUse` rather than on the suite it added.
`feedback/2026-08-13-214708` read the clause "which is how a single spec or
project is selected" as the sanctioned way to narrow a run, went to a local
Playwright invocation, and got no evidence out of it at all;
`feedback/2026-08-13-214729` reports that the suite blocks on a read from
`/dev/tty`, so a session without a controlling terminal loses the instance and
is told SUCCESS.

The **Assumed** is the half that failed. The commands the script prints are
`npm --prefix=Build run playwright:run -- --project e2e` and its `:open`
counterpart, `Build/package.json` carries `playwright:install` as a script of
its own, and the container path runs `IMAGE_PLAYWRIGHT` instead — so the local
route has a precondition this entry never names, and the reporting session hit
it as a browser build that was not on the host. The first **Wrong if** did not
fire as written: what stopped that session was the route the entry offered, not
the instance.

The statement holds and the clause is true. Every e2e case builds `COMMAND` from
`PLAYWRIGHT_PROJECT` alone and reaches no `"$@"` on `.checkouts/main`, `14.3`
and `13.4`, where `-s unit`, `-s functional`, `-s npm`, `-s composer` and
`-s phpstan` all do. That makes it the wrong sentence to hang a route on: a
Playwright-only diff costs the whole suite, and the price is what belongs beside
the command. `todo/open/2026-08-14-002523` carries the rewrite.
