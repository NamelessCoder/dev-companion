---
date: 2026-08-24T22:50:44+00:00
category: wrong-answer
status: open
model: claude-opus-5[1m]
tool: typo3_test_run_guide, anytestingbrowser-check
directory: /home/benji/projects/typo3-cms
---

# e2e-prepare without a tty leaves its containers running, contrary to what the guide states

## Observation

Task: boot the core's e2e instance from a non-interactive session to take backend screenshots.

typo3_test_run_guide says of e2e-prepare: "a run that has none [a controlling terminal] exits at the prompt, removes the instance it just installed and still reports SUCCESS." It also offers the workaround `script -qec '<cmd>' /dev/null` with stdin from a fifo.

I ran exactly that workaround:
  mkfifo $S/fifo; ( exec 3>$S/fifo; sleep 3000 >&3 ) &
  nohup script -qec "CI=true ./Build/Scripts/runTests.sh -s e2e-prepare -d sqlite" /dev/null < $S/fifo > $S/e2e.log 2>&1 &

Observed: the script exited with code 0 and its log stopped right after the composer install, with no instance URL printed. By the guide's sentence I should have concluded the instance was gone, and I very nearly wrote that in my report.

What actually happened: the containers stayed up. `docker ps` showed ac-web-14482 and ac-phpfpm-14482 running, `docker port ac-web-14482` gave 80/tcp -> 127.0.0.1:35082, and `curl http://127.0.0.1:35082/typo3/` returned 200 with `<title>TYPO3 CMS Login: New TYPO3 site</title>`. The whole browser session then ran against that instance for the rest of the task.

Reading Build/Scripts/runTests.sh explains it: runPlaywright starts phpfpm with `-d` and no `--rm`, and the web container with `--rm -d`; the wait is `while read -r _; do ... done </dev/tty` further down. When the read fails there is no cleanup step that stops those containers, so they survive the script.

So the statement is wrong in the half that matters most to a headless caller. It also means a headless session that believes the guide will tear down a working instance, or give up on the browser check entirely, when in fact it only needs `docker port` to find where the instance is listening. Two sessions could reach opposite conclusions from the same run.

I did not verify whether the workaround itself is at fault (script gave a pty but the run still exited early) or whether the containers survive on a plain no-tty run too. Either way the sentence about removal did not hold here.

## Query

typo3_test_run_guide(paths=["typo3/sysext/backend/Classes/Controller/Wizard/LocalizationController.php", ...], query="functional test for a backend controller") — the e2e-prepare suite entry; and typo3_rule_lookup(documentId="any/testing/browser-check")

## Suggestion

Correct the e2e-prepare note to say what a headless run actually leaves behind, and give the recovery instead of only the warning:

"Without a controlling terminal the script exits at its prompt and reports SUCCESS, but the containers it started stay up: ac-phpfpm-<suffix> and ac-web-<suffix>. The instance is still reachable — read the published port with `docker port ac-web-<suffix>` (a random high port on 127.0.0.1, not a fixed one). Stop them with `docker rm -f ac-web-<suffix> ac-phpfpm-<suffix>` when done."

That turns the entry from a reason not to try into a working headless recipe, which is what a non-interactive agent needs. Worth re-checking whether the `script -qec ... /dev/null` workaround the same entry recommends actually carries the run past the prompt — in my run it did not, and the entry presents it as the way through.
