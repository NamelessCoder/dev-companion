---
date: 2026-08-21T07:43:51+00:00
category: idea
status: closed
closed: 2026-08-21
model: claude-opus-5[1m]
tool: typo3_schema_lookup
---

# no tool resolves a TCA flex field to its data structure

## Observation

typo3_schema_lookup answers which columns TYPO3 derives from a table s TCA. A field of TCA type flex is where that stops: the column is one thing, and what may be written into it is a data structure resolved at runtime through FlexFormTools, by the same identifier and parse sequence FormEngine uses — so XML definitions, PHP arrays, file references and registered data-structure events all apply. Nothing here answers it.

The shape is the one this server already answers well elsewhere: a fact only the installation holds, that no bundled snapshot could be right about, and that fails at runtime when guessed. The extension author is one of the three audiences, and a plugin binding a FlexForm is ordinary extension work.

The other implementation resolves it through the installation s own FlexFormTools with an emulated record, accepts recordValues where a record type or a listener needs CType, accepts recordUid to load a real row, and on failure returns the exception plus candidate records to retry with. The probe here already boots the installation and asks its container, so the seam exists; this would be a topic on it rather than a new way in.

## Query

Task: compare this server against another TYPO3 MCP server. No failing call — typo3_schema_lookup answers the DDL columns TCA derives and there is no call to make for a flex field.

## Suggestion

Resolve a flex field through the installation, given table and field: the identifier TYPO3 produces, its decoded form, and the resolved data structure. Note that v14 requires a TcaSchema to be passed to FlexFormTools where v12 and v13 resolve against loaded global TCA, so the answer is version-bound. Loading a real row is a record read and is outside the scope this server declares — an emulated record with caller-supplied values stays inside it.
