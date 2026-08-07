---
date: 2026-08-07T13:24:57+00:00
category: bug
status: closed
closed: 2026-08-07
model: claude-opus-5[1m]
tool: typo3_documentation_lookup
directory: /home/benji/projects/typo3-cms
---

# documentation page read returns TCA property tables with headers but no values

## Observation

Task: review a TYPO3 core commit about empty-date handling in Extbase; I needed to know what the TCA type=datetime "format" values timesec and datetimesec mean for storage, and what the default of "nullable" is depending on dbType.

The prose and the code examples came back intact and were decisive — the sentence "The format option defines how the field value will be displayed, for example, in FormEngine. The storage format is defined via dbType and falls back to eval=integer" settled the question I was asked and stopped me adding three redundant test variants.

But every property definition list on that page came back as bare labels with the values dropped. Under "nullable" the text reads literally: "**nullable**\n\n**Type**\n\n**Default**\n\n**Path**\n\n**Scope**\n\nIf nothing is entered into the field, then it will be saved as NULL." So Type, Default, Path and Scope are each named and each empty. The same under dbType, format, range, readOnly, searchable, softref, mode, placeholder and the behaviour sub-properties.

The default of "nullable" per dbType was exactly one of those dropped cells, and it was load-bearing for the review: whether a native DATE column can carry nullable=false decides whether a reachable TCA configuration reaches the changed code path. I got it instead by reading typo3/sysext/core/Classes/Schema/Field/DateTimeFieldType.php in the checkout, where isNullable() returns $this->configuration['nullable'] ?? true when a dbType is set.

Dropping the values while keeping the labels is worse than dropping both, because the output reads as though the property genuinely has no documented default.

## Query

typo3_documentation_lookup page="https://docs.typo3.org/m/typo3/reference-tca/14.3/en-us/ColumnsConfig/Type/Datetime/Index.html" targetVersion="14"

## Suggestion

Carry the definition-list values through the HTML-to-text conversion for docs.typo3.org pages. On the TCA reference these tables hold the machine-readable half of every property — type, default, path, scope — and that is the half a caller checking a configuration most often wants. If the values cannot be extracted reliably, omit the labels too rather than emitting them empty, so a caller can tell "not in this page" from "the page says nothing".
