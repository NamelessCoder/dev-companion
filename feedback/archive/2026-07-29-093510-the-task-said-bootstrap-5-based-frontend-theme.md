---
date: 2026-07-29T09:35:10+00:00
category: wrong-answer
status: closed
closed: 2026-07-29
commit: 13c8741
subject: "Answer the backend's CSS as the backend's"
tool: typo3_architecture_lookup
---

# The task said "Bootstrap 5 based frontend theme" and the answer was four backend CSS hints presen...

## Observation

The task said "Bootstrap 5 based frontend theme" and the answer was four backend CSS hints presented without qualification: "Treat Build/Sources/Sass/ as the source of truth", "Bootstrap usage is being actively reduced, treat Bootstrap as existing infrastructure, not as the preferred target architecture", "prefer existing --typo3-* custom properties", "CSS changes must work in both light and dark backend color schemes". For a frontend theme extension whose entire purpose is Bootstrap 5 (sources under Resources/Public/Scss/bootstrap5/, compiled at runtime by scssphp, no --typo3-* tokens, no backend colour scheme) every one of those is the opposite of correct. The css domain matched on the file extension alone; the words "frontend theme" and the non-core path did not narrow anything. Same effect in a second call: paths Classes/ViewHelpers/Format/ScssViewHelper.php + Configuration/Services.yaml pulled in the backend CSS hint purely because of the string "Scss" in a PHP class name.

## Query

typo3_architecture_lookup {"task":"Sass architecture, variables and build pipeline for a Bootstrap 5 based frontend theme","paths":["Resources/Public/Scss/bootstrap.scss","Build/Sources/Sass/_variables.scss"]}

## Suggestion

Scope the CSS/Sass hints explicitly to the backend — both in their title and in a domain guard — and do not match them for paths outside typo3/sysext/backend|core|styleguide, nor for a .php filename that merely contains "scss"/"css". Stating "these conventions describe TYPO3's backend CSS; frontend theming is out of scope, see docs.typo3.org" would be more useful here than four confidently wrong hints.
