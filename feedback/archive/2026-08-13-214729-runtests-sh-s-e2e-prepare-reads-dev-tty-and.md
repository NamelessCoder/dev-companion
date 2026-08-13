---
date: 2026-08-13T21:47:29+00:00
category: missing-knowledge
status: closed
closed: 2026-08-13
model: claude-opus-5[1m]
tool: typo3_test_run_guide, typo3_script_lookup
directory: /home/benji/projects/typo3-cms
---

# runTests.sh -s e2e-prepare reads /dev/tty and tears the instance down in a non-interactive sessio...

## Observation

Task: review Gerrit change 93319, a Playwright-only diff; I wanted a running instance to try the two new specs against.

I ran `CI=true ./Build/Scripts/runTests.sh -s e2e-prepare` from a background shell with no controlling terminal. It installed the whole test instance — several minutes of composer work — printed the success banner and the PLAYWRIGHT_BASE_URL, and then immediately died:

  ✓ Environment prepared. You can now press Enter to run all tests or run playwright locally...
  (Press Control-C to quit, Enter to run tests in container)
  ./Build/Scripts/runTests.sh: line 224: /dev/tty: No such device or address
  Remove container for network "typo3-core-3066"
  Result of e2e-prepare ... SUCCESS

Two things are wrong with that from a caller's side. It reports SUCCESS while having done the opposite of what the suite is for — the instance it exists to leave standing was removed. And `curl` against the printed URL returned 000 within seconds, so the URL in the success banner was already dead when I read it.

This is specifically not covered by the guidance that does exist. typo3_test_run_guide's invocation notes say: "Prefix scripted and non-interactive runs with CI=true. It drops the interactive container flags, skips the SIGINT trap, and selects the CI phpstan config. Without a TTY the script also removes the interactive flags on its own, but CI=true is the explicit form and the one to use from an agent." I had done exactly that. CI=true does not cover the `read` from /dev/tty at line 224, and the note reads as though it does.

I worked around it by allocating a pty and holding stdin open with a fifo nothing ever writes to:

  mkfifo stdin.fifo
  (exec 9>stdin.fifo; while true; do sleep 3600; done) &
  script -qec "./Build/Scripts/runTests.sh -s e2e-prepare" /dev/null < stdin.fifo

That worked and left the instance up. Getting there cost one failed attempt with `< /dev/zero`, which floods the pty with NUL bytes and produced a 2.2 MB log of nulls before I killed it. The whole detour was mine to work out; nothing in the server pointed at it.

PLAYWRIGHT_USE_EXISTING_INSTANCE=1 is documented in the same answer and is the thing that made the retry cheap, so that note earned its place.

## Query

CI=true ./Build/Scripts/runTests.sh -s e2e-prepare — run from an agent session with no controlling terminal

## Suggestion

Add to the e2e-prepare entry, and to the invocation notes beside the CI=true sentence, that the suite blocks on a read from /dev/tty and therefore needs a controlling terminal — CI=true does not substitute for one. Say what happens without one: the script exits at the prompt, removes the instance, and still reports SUCCESS, so the banner and the exit code both lie.

Give the working non-interactive form, since a session that needs a standing instance has no other way to get one: a pty via `script -qec`, with stdin held open by something that never writes. And name PLAYWRIGHT_USE_EXISTING_INSTANCE=1 in the same breath as the thing that makes a second attempt cheap — that pairing is what turned a fifteen-minute failure into a two-minute retry.

The same question is worth asking of e2e-install-prepare, e2e-browser and e2e-install-browser, which all appear to end in the same prompt.
