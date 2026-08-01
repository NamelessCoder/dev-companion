---
date: 2026-07-29T09:42:29+00:00
category: tool-gap
status: closed
closed: 2026-07-29
commit: 7558e09
subject: "Say which repository a core script runs in"
tool: typo3_test_run_guide, typo3_script_lookup
---

# typo3_task_guide at least computes outsideCore; typo3_test_run_guide and typo3_script_lookup have...

## Observation

typo3_task_guide at least computes outsideCore; typo3_test_run_guide and typo3_script_lookup have no such notion at all. Given two paths that are plainly not core — they begin with extensions/ and contain a project extension key — typo3_test_run_guide returned the complete runTests.sh catalog: 18 suites, the full options table, invocation notes and seven examples, every one of them referencing typo3/sysext/ paths and a Build/Scripts/runTests.sh that does not exist in this repository. Nothing in the payload indicated any doubt about applicability. The cost is not only correctness: it was by a wide margin the largest response of the session, so an agent pays a heavy context price for an answer that is entirely inapplicable. typo3_script_lookup behaved the same way — asked explicitly about php-cs-fixer and phpstan in a composer-based project, it returned the runTests.sh script document twice, with a coverage score of 0.5, and never signalled that the question was outside its scope. The right answer for this project was discoverable in seconds from composer.json: t3g:cgl runs php-cs-fixer against .php-cs-fixer.dist.php and t3g:phpstan runs phpstan against build/phpstan.neon.

## Query

typo3_test_run_guide{paths:["extensions/events_sitepackage/Classes/Form/Element/IconSearchElement.php","extensions/events_sitepackage/Resources/Public/Css/backend-icon-search.css"]} and typo3_script_lookup{task:"CGL php-cs-fixer and phpstan in a composer based project"}

## Suggestion

Apply the same outsideCore classification typo3_task_guide already has to typo3_test_run_guide and typo3_script_lookup, and treat it as a routing decision rather than a label: when the given paths are outside typo3/sysext/, return a short refusal naming why instead of the core catalog, so the wrong answer is also the cheap one. Something like {"outsideCore":true,"suites":[],"notice":"These paths are outside typo3/sysext/. runTests.sh is a core checkout script and does not apply here. This project's checks are defined in its own composer.json scripts and CI workflow."} would be both more correct and a fraction of the tokens. A path-shape heuristic is enough — a leading typo3/sysext/ segment versus anything else — and it needs no access to the checkout.
