---
date: 2026-08-24T20:51:13+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_configuration_lookup
directory: /home/benji/projects/typo3-cms
---

# Read DefaultConfiguration.php by hand four times without ever trying typo3_configuration_lookup

## Observation

Task: reviewing Gerrit change 93079, which reworded the XLF label labels.generatePassword.failed to say "please check the Administration log for details". I had to establish whether that sentence is true.

Four questions, all about resolved configuration, all answered by grepping typo3/sysext/core/Configuration/DefaultConfiguration.php with python and grep:

1. Which log writers are configured by default — I needed to know that LOG/writerConfiguration has FileWriter at WARNING globally and DatabaseWriter only under Core/Resource/ResourceStorage, which is what makes the label's promise false.
2. Which caches are in the "pages" group (for an unrelated change, 95396).
3. Which password policies SYS/passwordPolicies ships and which of them carry a generator key.
4. What BE/passwordPolicy and FE/passwordPolicy default to.

I never called typo3_configuration_lookup. I did not consciously reject it — it was not in the ToolSearch results I pulled, and I never went back to the tool list once I was inside the reading. Its description says it answers "the resolved configuration value, after every extension has had its say", which is strictly better evidence than the default file I read: the default file says what ships, the resolved value says what this installation actually runs, and my findings were claims about installations.

The gap this leaves in my own report: I wrote "the global default writer for WARNING and above is FileWriter only" on the strength of the default file. That is a statement about the shipped default, not about the installation, and I presented it as settling what an administrator would find. typo3_configuration_lookup would have made it a statement about a real resolved value.

Same pattern for question 3: I wrote a crude regex over a text slice, and it produced two false positives ("routing", "Response") that were neighbouring config keys rather than password policies. I noticed and discounted them, but a structured answer would not have produced them.

## Query

Questions asked of DefaultConfiguration.php by grep instead of the tool: LOG/writerConfiguration; SYS/caching/cacheConfigurations groups containing 'pages'; SYS/passwordPolicies (names, and which define a 'generator'); BE/passwordPolicy and FE/passwordPolicy defaults. Session task: review of Gerrit change 93079 (issue #109162) plus a second fix in typo3/sysext/backend/Classes/Form/FieldControl/PasswordGenerator.php (issue #110532).

## Suggestion

Two things, one of them not about the tool itself.

First, the routing. typo3_task_guide with changeType "audit" returned a nextTools list, and typo3_configuration_lookup was not on it — the list named project_describe, extension_describe, changelog_lookup, hint_lookup, test_run_guide, task_guide, feedback_record. A review whose findings turn on TYPO3_CONF_VARS is common enough that configuration_lookup belongs in that list, and the base.md ordering in typo3-core-patch-review does mention it under "What each runtime lookup adds" but as one of five in a paragraph I read before I knew which configuration questions the diff would raise. Naming it in nextTools at the point the paths are known would have caught me.

Second, if it can, let it answer "which keys exist under this path" and not only "what is the value at this path". Three of my four questions were enumerations — which writers, which caches in a group, which policies carry a generator — rather than single-value reads, and enumeration is what sends a caller to the source file.
