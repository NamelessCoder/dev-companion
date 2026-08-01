---
date: 2026-07-28T15:10:21+00:00
category: missing-knowledge
status: closed
closed: 2026-07-28
commit: 05c7527
subject: "Write down the changelog file format"
tool: typo3_rule_lookup
---

# Changelog entries are the fourth most-touched thing in core history (1243 file touches under typo...

## Observation

Changelog entries are the fourth most-touched thing in core history (1243 file touches under typo3/sysext/core/Documentation/Changelog/ in 18 months), and almost every feature, deprecation and breaking change patch needs one. The server knows only that they exist, where they live, the filename prefixes, and that checkRst should be run. The actual file format is not documented anywhere, and it is rigid enough that an agent will not reproduce it from the prefix alone. A current example, typo3/sysext/core/Documentation/Changelog/15.0/Feature-109444-AddDefaultValueSupportForCountrySelectFormElement.rst, shows the required shape: it opens with "..  include:: /Includes.rst.txt" (two spaces after the dots), then a reference anchor of the form ".._feature-<issue>-<unix timestamp>:", then the title in the exact form "Feature: #109444 - <Title>" fenced by overline and underline rows of "=" that must match the title length, then "See :issue:`109444`", then the Description and Impact sections, and it closes with a ".. index:: Backend, ext:form" directive. Prose uses role markup such as :yaml:, :php:, :typoscript: rather than plain literals. The filename itself follows <Type>-<issue>-<UpperCamelCaseTitle>.rst. Get the anchor, the title fence width or the index directive wrong and checkRst fails or the entry renders wrong on docs.typo3.org — a slow, annoying review round for something purely mechanical that the server is well placed to hand over ready-made.

## Query

query="changelog rst file format required directives"

## Suggestion

Document the changelog RST format as a section, ideally with a complete skeleton an agent can fill in: include directive, anchor with the issue number and timestamp, title fence rules, the See :issue: line, the expected sections per type (Description and Impact for Feature; Description, Impact and Migration for Deprecation and Breaking), the closing .. index:: directive with its allowed keywords, and the filename pattern. Since typo3_commit_message_help already drafts a commit message from a changeType and an issue number, the natural extension is to have it, or a companion tool, emit the matching changelog skeleton for the same issue — the inputs are identical and the output is fully mechanical.
