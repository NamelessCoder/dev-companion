---
date: 2026-08-24T11:17:28+00:00
category: idea
status: closed
closed: 2026-08-24
model: claude-opus-5[1m]
tool: typo3_project_describe, typo3-core-patch-development
directory: /home/benji/projects/typo3-cms
---

# Supersedes the previous correction: the fixer script stays, only PHP_CS_FIXER_IGNORE_ENV goes

## Observation

Task: same session. This supersedes feedback/2026-08-24-111637 ("point at the containerised cgl suite instead of prefixing the direct fixer"), which overshot, and restores what the first feedback of the session said.

I read the user's "wir sollten das ignore env nicht nehmen" as a rejection of the script and removed Build/Scripts/cglFixMyCommit.sh from AGENTS.md in favour of runTests.sh -s cglGit. That was wrong. Their next sentence: "das script kann ruhig aufgerufen werden aber das ignore env ist halt mist". Only the environment override is objected to, not the script.

So the picture that holds is the one the first feedback described, and the two halves fit together rather than competing:

- PHP_CS_FIXER_IGNORE_ENV=1 makes php-cs-fixer run on a PHP version it does not support. Dropping it means the tool reports a wrong interpreter instead of being muted, which is the point.
- Once it is dropped, the script has to be run under a PHP that is actually supported. In a checkout with a local DDEV environment that is `ddev exec ./Build/Scripts/cglFixMyCommit.sh` — the correction the user made to me at the start of the session.

AGENTS.md now reads `./Build/Scripts/cglFixMyCommit.sh` in both places, with no override and no prefix, because .ddev/ is untracked here and a prefix in a versioned file would be the machine-specific preference that file forbids. The prefix belongs to the checkout, which is where a tool reading .ddev/config.yaml can see it.

## Query

Supersedes feedback/2026-08-24-111637-correction-point-at-the-containerised-cgl-suite.md; confirms feedback/2026-08-24-104546-cglfixmycommit-sh-needs-ddev-exec-where-the.md

## Suggestion

Work off the first feedback of the session as filed and disregard the 11:16:37 correction. Concretely: where typo3_project_describe reports an environment it found and did not enter, say which of the listed commands run through it — Build/Scripts/cglFixMyCommit.sh does, the runTests.sh suites do not. Do not tell anyone to stop recommending the script, and do not suggest PHP_CS_FIXER_IGNORE_ENV as the way to make it run outside an environment: that is the workaround this developer rejects, and the reason is that it silences the version check the fixer is right to make.
