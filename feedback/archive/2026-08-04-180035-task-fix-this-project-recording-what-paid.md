---
date: 2026-08-04T18:00:35+00:00
category: idea
status: closed
closed: 2026-08-04
model: claude-opus-5
tool: typo3_hint_lookup, typo3_changelog_lookup, typo3_extension_describe, typo3_icon_lookup
directory: /home/benji/projects/site-new
---

# Task: "fix this project". Recording what paid, because it is what must not be broken.

## Observation

Task: "fix this project". Recording what paid, because it is what must not be broken.

Four hints each produced a finding I could not have reached by reading the checkout:
- environment-variables → the High finding. A readme documented four env vars; nothing read them. The hint's own sentence about a documented name without a getenv() line is the finding.
- content-element-preview → three Medium findings. GridColumnItem renders the preview header before dispatching the event the Fluid renderer answers, so a template rendering {record.header} prints it twice. Unfindable from the templates; I confirmed it at GridColumnItem.php:81 only because the hint told me where to look.
- language-files → two findings plus a project-owned lint rule. target-language is optional in XLIFF 1.2 and XliffLoader::parseXliff1() decides on that attribute alone, so a translation missing it is schema-valid and silently discarded. Worth noting this knowledge was added by earlier feedback (closed as "[TASK] Say what a missing target-language costs a translation file") — it paid off twice in this session.
- project-configuration-files → the DDEV file-ownership rule, which produced two findings and later diagnosed a regression I had introduced myself.

Two things prevented FALSE findings, which is worth as much:
- sitepackage-templates plus the package's own settings.yaml explained that <f:layout name="Default"/> in a content element resolves to fluid_styled_content's frame and is deliberate. I had it queued as a bug.
- typo3_icon_lookup validated six identifiers in six single calls, all exact matches, so I filed nothing about them. Cheap and decisive.

typo3_changelog_lookup's design deserves specific credit: every response returns the full tag list for that version and type. That let me write an eight-call deprecation sweep bounded by real tags instead of guesses, and ext:seo / ext:fluid-styled-content / ext:impexp being absent from the list answered "no v14 deprecations there" by absence rather than by an empty search I would have had to distrust.

typo3_extension_describe's artifacts block — manual, readme, tests, and languageFiles with the source language each declares — is the highest-value single response of the session. sourceLanguage: "de" on all four XLF files was a finding straight out of the answer, and re-running it after the work showed sourceLanguage: "en" with translations: ["de"] and manual/readme populated, which is how I verified two items were actually closed.</observation>
<parameter name="suggestion">No change requested. If the hints are ever condensed, keep the causal sentences rather than the summaries — in all four cases what made the finding was one sentence naming the mechanism and the class it lives in, not the surrounding advice. Keep the tag list in every changelog response; it is what makes a bounded sweep writable before a file is opened.</suggestion>
</invoke>

## Query

Whole session: "fix this project" on a TYPO3 14.3.5 Composer project with one sitepackage — audit, then ten items worked off. Naming the calls that each produced a finding, so they survive later editing.
