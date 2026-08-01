---
date: 2026-07-28T15:06:46+00:00
category: missing-knowledge
status: closed
closed: 2026-07-28
commit: 704505d
subject: "Cover TypoScript, site sets, and TSconfig"
tool: typo3_core_task_brief
---

# TSconfig is unknown to the server. typo3_rule_lookup returns matchCount 0 for a plain page/user T...

## Observation

TSconfig is unknown to the server. typo3_rule_lookup returns matchCount 0 for a plain page/user TSconfig query, and typo3_core_task_brief for a realistic TSconfig feature returned intents, architectureHints, rules and checks all empty — the only output was the checkRst suite and the generic checklist. An agent asking about a TSconfig change gets nothing to work from. To its credit this is the honest failure mode, unlike the .typoscript and .tsconfig paths reported separately, which get answered with the CSS Browser Target section; but the result is still zero help on a very common kind of core patch. The concrete conventions, verified at the pinned revision: Configuration/page.tsconfig and Configuration/user.tsconfig in a system extension are auto-loaded, present in about a dozen sysexts, and replace the ext_localconf.php registration; a new option needs a Feature-*.rst changelog entry and documentation; the option is read through the merged page TSconfig, in FormEngine specifically via the PageTsConfigMerged FormDataProvider, which ties into the provider-ordering gap reported separately. Note also the name collision: "tsconfig" means TYPO3 page/user TSconfig here, but also tsconfig.json in the Build directory. A knowledge section should disambiguate explicitly, because the two are unrelated and an agent will conflate them.

## Query

task="Add a new page TSconfig option to configure the file list module and document it", area="filelist", changeType="feature"; typo3_rule_lookup query="page TSconfig user TSconfig defaults for a backend module"

## Suggestion

Cover TSconfig in the architecture knowledge, together with the TypoScript and site sets section suggested separately but clearly distinguished from it: where page.tsconfig and user.tsconfig live and that they are auto-loaded, how an option is read at runtime, that a new option needs a changelog entry plus documentation, and how TSconfig relates to FormEngine via PageTsConfigMerged. Add "TSconfig", "page TSconfig" and "user TSconfig" as retrieval keywords to typo3_rule_lookup, and state the tsconfig.json disambiguation in the section itself.
