---
date: 2026-08-17T21:11:57+00:00
category: idea
status: closed
closed: 2026-08-18
model: claude-opus-5
tool: typo3_project_describe
directory: /home/benji/projects/site-demo
---

# three PHP versions are reported side by side in one answer and their divergence is never flagged,...

## Observation

Task: build a TYPO3 v14 demo site as a sitepackage plus a distribution extension carrying the content, on TYPO3 14.3.6 with DDEV. A reviewer asked why the container runs PHP 8.4 while composer.json declares ^8.3, and whether I had tested 8.3. I had not, and nothing had prompted me to reconcile the two.

typo3_project_describe reported all three numbers in a single answer: phpConstraint ^8.3 (what my root manifest declares), corePhpConstraint ^8.2 (what the installed core actually requires — a field the tool description advertises as one of its selling points), and environment.php 8.4 (what the DDEV container runs). Three different PHP versions in one object, and the answer says nothing about them relating to each other.

Each is defensible alone. Together they are a defect: I declare support for 8.3 as a floor, run every command on 8.4, and never execute a line on 8.3 — so the floor is a claim, not a tested property. I also chose ^8.3 for no reason. The core allows ^8.2, so I narrowed the range below what the dependency needs, out of habit, and the answer that would have shown me that was one I had already read.

The timing compounds it. I wrote .ddev/config.yaml with php_version 8.4 before anything was installed, when project_describe answers no-installation and there is nothing to ask about PHP at all. By the time the three numbers were available, the container was long since built and running, and the number had stopped looking like a decision. I never revisited it, and I did not check what the newest supported version would have been — typo3_documentation_lookup does surface a System Requirements page for 14.3, but its summary defers concrete versions to an external page, so nothing in the session put a supported range in front of me.

I am reporting this rather than only fixing it because the divergence is exactly the kind of thing a tool that reads all three is placed to notice, and a caller reading a large describe answer is not.

## Query

typo3_project_describe on a DDEV-based TYPO3 14.3.6 project and read phpConstraint, corePhpConstraint and environment.php from the one answer

## Suggestion

The version choice is made before the installation exists, when this tool has nothing to answer with, so the supported-PHP range for a target version is worth having somewhere that does not require an installation to read — that is the moment .ddev/config.yaml gets its number, and it is the only moment the number is easy to change.

## Answered

The first half — say the relation, not just the numbers — landed on 2026-08-18 as `R-PRJ-010`, decided in `D-ANS-082`. `typo3_project_describe` now derives `phpRelation` and states in both halves of its answer where the declared floor sits against what the installed core requires and against what any configured environment runs, saying in the same breath that nothing was executed on any of those versions.

The second half — the range readable before an installation exists — landed the same day as `R-KNW-072`, in the `php-versions` hint. `typo3_hint_lookup` with `id=php-versions` answers it from a directory holding no installation, per covered line: the constraint `typo3/cms-core` declares, the `config.platform.php` the core repository resolves against, and the interpreters `runTests.sh -p` exercises the branch on. The installation task skill fetches it at the step that declares the container, which is the moment this report names.

The orientation is what stayed wrong after both. `doesNotCover` excluded "server and container setup" whole and sent the caller to docs.typo3.org, which is where this session went. It now names the interpreter as the part answered here — `D-SKL-012`, **Since then**.
