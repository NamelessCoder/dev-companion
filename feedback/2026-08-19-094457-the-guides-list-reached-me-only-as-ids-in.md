---
date: 2026-08-19T09:44:57+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_rule_lookup, typo3_project_describe
directory: /home/benji/projects/blog
---

# The guides list reached me only as ids in project_describe, and I read none of them until too late

## Observation

Task: full audit of the blog extension before its v14 release, followed by fixing three of the findings.

My client renders no resource list. The only place the server's documents reached me was typo3_project_describe's guides array, sixteen ids with titles, at the very start of the session. The base.md of the extension-health skill says exactly this: "that list is the only place they are named to a client that renders no resource list". That mechanism worked — but a list of sixteen ids in the middle of a large orientation answer, before I knew what the task would need, is not something I came back to. I read none of them during the audit itself.

Two of them were directly about what I later did by hand:
- extension/compatibility/running-on-a-declared-major-that-is-not-installed. I improvised it: rm composer.lock, composer require typo3/cms-core:^13.4 (which failed twice — once because the copied lockfile pinned everything and -W was not enough, once because composer's audit blocked the 13.4 releases as insecure), then composer dump-autoload because the downgrade left a stale autoloader, then discovering PHPUnit 12 needs PHP 8.3 while the container has 8.2. Its topic list, which I only saw later, includes "A Composer Root of Its Own", "What the Second Root Resolves Differently" and "Which Checks Are Worth Re-running There" — that is the page I needed, and I had already finished the procedure worse when I first saw its title.
- extension/testing/phpunit, with its "Database credentials for the functional suite" section. Filed separately.

How I finally saw the list properly: I called typo3_rule_lookup with a documentId I had mis-transcribed from the earlier project_describe answer — "extension/compatibility/running-a-package-on-a-declared-major-that-is-not-installed" instead of "running-on-a-declared-major-that-is-not-installed". It returned matchCount 0 and, as a side effect, the complete document list with topics. That accidental call is the only reason I know what these pages contain. The failure mode is good and I would not change it; the fact that it took a typo to surface the catalogue is the finding.

I did read one document properly in the end — extension/compatibility/a-declared-major-that-is-not-installed was fetched but I did not act on it, because by then I had already installed v13 and could read the branch directly, which is the stronger evidence the page itself would presumably recommend.

So: none read end to end during the work they would have carried. One read after the fact. The rest never opened.

## Query

typo3_project_describe (guides array, 16 ids) at session start; typo3_rule_lookup documentId="extension/compatibility/running-a-package-on-a-declared-major-that-is-not-installed" (mis-transcribed, matchCount 0, returned the full document list)

## Suggestion

Surface the relevant guides at the moment the task is known rather than only at orientation. typo3_task_guide already returns a guides array and, for my call (changeType=audit, targetVersion=14), it came back empty — while at least two documents in the catalogue were about steps that audit went on to need. If the guide brief named "running-on-a-declared-major-that-is-not-installed" for any task whose repository declares a major that typo3_project_describe reports as not installed, it would have reached me at the right moment. That condition is already computable from project_describe's own output: it reports coreConstraint ^13.4.15 || ^14.3 || 15.*@dev against typoVersion 14.3.6.

Second, smaller: when typo3_rule_lookup is given a documentId that does not exist, it already returns the full list. Consider saying so explicitly in that answer — "no document with this id; the ids are:" — because I read the empty matches first and nearly moved on before noticing the catalogue underneath.
