---
date: 2026-08-24T12:23:48+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_gerrit_lookup, typo3_project_describe
directory: /home/benji/projects/typo3-cms
---

# nothing names the currently maintained release branches, which is what a Releases: footer needs

## Observation

Task: review Gerrit change 95179 and work off its review comments. Both reviewers said "no 13.4 backport", so rewriting the `Releases:` trailer was one of the two deliverables.

The commit said `Releases: main, 14.3, 13.4`. Dropping 13.4 leaves `main, 14.3` — but only if a branch called 14.3 is actually the maintained line, and I had no way to check that on this server. typo3_project_describe told me the checkout is 15.0.0-dev; it says nothing about which other branches exist or are maintained. typo3_gerrit_lookup told me this change targets `branch: main` and nothing about its siblings (the change had no backport sharing its Change-Id yet, so the "answered together with the changes sharing its Change-Id" route gave me nothing).

I settled it from the checkout instead: `git branch -r | grep -E "origin/1[3-5]"` returned origin/13.0 13.1 13.3 13.4 14.0 14.1 14.3, and `ls typo3/sysext/core/Documentation/Changelog/` showed 13.4.x and 14.3.x as the two backport folders. From those two facts together I concluded 13.4 and 14.3 are the maintained lines and 14.3 is the right survivor. That inference is sound in a full core clone and unavailable in a shallow one or when the remote is stale — mine was 59 commits behind origin/main when I started.

This is the same fact the changelog placement rule needs (the .x folder for the oldest backported branch), so getting it wrong is wrong twice.

## Query

Not asked. Established with: git branch -r | grep -E "origin/1[3-5]"; ls typo3/sysext/core/Documentation/Changelog/; grep -n "BRANCH|VERSION" typo3/sysext/core/Classes/Information/Typo3Version.php.

## Suggestion

Answer, from knowledge rather than from the checkout's remote refs, which TYPO3 core branches are currently maintained and what each is for: which is the development branch, which is the current sprint-release branch, which are the LTS branches still receiving bugfixes, and which are ELTS or dead. That is the fact a `Releases:` trailer is built from and the fact a changelog .x folder is built from, and it changes a few times a year — exactly the kind of thing a knowledge server should hold and a checkout should not have to be trusted for. typo3_project_describe would be a natural place (it already reports the installed version and could state where that version sits in the maintenance timeline), or typo3_gerrit_lookup could carry it alongside the change's own branch so that "which branches may this be backported to" is answered by the call that already told me it targets main.
