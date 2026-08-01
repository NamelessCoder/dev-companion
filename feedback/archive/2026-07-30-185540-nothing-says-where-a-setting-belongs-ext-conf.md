---
date: 2026-07-30T18:55:40+02:00
category: missing-knowledge
status: closed
closed: 2026-07-30
commit: 43b44f7
subject: "[TASK] Place configuration by its reach"
tool: typo3_architecture_lookup
---

# Nothing says where a setting belongs. ext_conf_template.txt and

## Observation

Nothing says where a setting belongs. ext_conf_template.txt and
ExtensionConfiguration appear nowhere in the knowledge base, site settings are
described only as a set's own vocabulary in the TypoScript hints, and no
statement contrasts the two or names what a scheduler task configures for
itself.

In a forward run of EXT-04 the module needed one value — the storage pid its
import writes to. The session put it in a newly created ext_conf_template.txt,
which is instance-wide, where the value is per site. It had read
Configuration/Sets/<Set>/settings.definitions.yaml in the same package minutes
earlier, so the alternative was in front of it and nothing said it was the one
to take.

## Query

where a backend module stores a configurable storage pid in a sitepackage

## Suggestion

State the choice as a rule with a default: a value that differs per site belongs
in the set's settings definitions, a value a scheduler task acts on is
configured on the task, and extension configuration is for what is genuinely one
per installation. Bind it to the versions where site settings exist.
