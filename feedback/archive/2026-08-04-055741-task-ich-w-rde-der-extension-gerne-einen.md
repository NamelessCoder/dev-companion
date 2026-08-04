---
date: 2026-08-04T05:57:41+00:00
category: idea
status: closed
closed: 2026-08-04
model: claude-opus-5[1m]
tool: typo3-extension-testing
directory: /home/benji/projects/ext-guidedtour
---

# Task: "ich würde der extension gerne einen codestyle fixer wie php-cs-fixer hinzufügen" — a Germa...

## Observation

Task: "ich würde der extension gerne einen codestyle fixer wie php-cs-fixer hinzufügen" — a German request naming php-cs-fixer, in a TYPO3 14.3 extension repository with no test or static-quality infrastructure.

The skill fitted and I would activate it again. Its description names php-cs-fixer explicitly, so the match was immediate even though the request was in German. What earned its place:

- SKILL.md routing me to exactly one of the three reference files (static-quality.md) instead of all three.
- static-quality.md naming the packages, and naming them as defaults for a checkout that covers a check with nothing rather than as replacements for what a project already runs.
- Its rule "keep automatic formatting inside the first-party paths the project intends it to touch" is what made me reject the shipped `->in(__DIR__)` template. This repository puts vendor-dir under .build/, and the rule set's excludes are literal directory names. Without that rule I would have shipped a config whose finder walks the vendored core.
- "Keep a check command apart from the one that writes" produced ci:php:cs / fix:php:cs, and the proof discipline ("run the fix command, run the check again, inspect the working tree") is what made me plant a deliberately misformatted probe file, watch the check fail with exit code 8, watch the fixer touch that file and nothing else, then delete it. On a repository that was already conformant, that probe is the only thing that distinguished "the check works" from "the check found nothing because it looked at nothing". I did the same for the editorconfig check.
- "Report a finding in code the task does not touch instead of quietly fixing it" is what stopped me rewriting two XLF files and the README on my own initiative. I reported them, the user answered "wir wollen allen coding standards folgen", and only then did I change them.

**The step no skill carried, and I worked out myself:** commit ordering. The moment was the user's last message, "ok gut dann committe miir das bitte", with an uncommitted tree holding tooling, a manifest change, a rewrapped README and two reindented XLF files. static-quality.md says "keep a formatting pass in its own commit, apart from behavioural change" — but says nothing about the order, and here the order is the whole problem. The obvious split (tooling first, formatting second) produces a first commit that introduces `ci:editorconfig` onto a tree whose XLF files still contain tabs: a commit that fails the check it just added. I inverted it — XLF whitespace first, then the commit that adds the checks and brings the README into line in the same breath — so that every commit is green at its own HEAD, and I verified that by running the checks at HEAD afterwards. I would work that out again next session, and so would anyone establishing a first check on a non-conformant repository, which is the ordinary case rather than an edge one.

**What I would drop:** nothing from static-quality.md. From base.md, the fixed five-step order is written for a code change; steps 3 and 5 (typo3_task_guide, the changelog deprecation sweep) did no work on a task that establishes tooling and touches no TYPO3 API, and I skipped both.

## Query

Skill typo3-extension-testing, activated with args "add php-cs-fixer code style fixer to the extension" for the request "ich würde der extension gerne einen codestyle fixer wie php-cs-fixer hinzufügen"

## Suggestion

Add one paragraph to references/static-quality.md, beside the existing "keep a formatting pass in its own commit": when a check is introduced onto a repository that does not yet pass it, the conformance commits come first and the commit that adds the check comes last, so that no commit fails the check it introduces — and verify by running the check at the new HEAD. Consider also letting base.md's order be shortened where a skill has already routed the task and the change touches no TYPO3 API: on this session steps 3 and 5 were prescribed and skipped, and a prescription that gets skipped teaches the next reader to skip the ones that matter too.
