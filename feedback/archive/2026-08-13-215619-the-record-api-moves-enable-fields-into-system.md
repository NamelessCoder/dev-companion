---
date: 2026-08-13T21:56:19+00:00
category: missing-knowledge
status: closed
closed: 2026-08-13
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3_documentation_lookup
directory: /home/benji/projects/typo3-cms
---

# the Record API moves enable fields into _system and unsets them from the properties, so $row['hid...

## Observation

Audit of an agent's own accumulated project notes against what this server answers. This one survived and is the most consequential of the set, because the failure is silent and reads as a correct value.

The note, established from the core source while working on the page module: TYPO3\CMS\Core\Domain\Record moves the enable and system fields into SystemProperties, and RecordFactory unsets them from the main properties array. So a Record-sourced row does NOT contain hidden, starttime, endtime, fe_group, crdate or tstamp.

- $record->toArray(true) returns the regular fields plus '_system' => SystemProperties::toArray().
- _system keys: isDisabled, isDeleted, isLockedForEditing, publishAt/publishUntil (DateTimeInterface), createdAt/lastUpdatedAt (DateTimeInterface), userGroupRestriction (int[] of uids including the special -1/-2), sorting, description, language, version.
- Accessors: $record->getSystemProperties()?->isDisabled().

The trap: reading $row['hidden'] on a Record-sourced row — PageLayoutContext::getLocalizedPageRecord() and getPageRecord() in the page module are the concrete case — is always empty, and empty reads as "not disabled". Nothing throws. A record that IS hidden renders as visible. FormEngine's databaseRow is the exact opposite shape: a flat array with the enable fields at top level, hidden as a scalar, datetimes as ISO strings, fe_group as an array. Code consumed by both — RecordIdentityRenderer is the core's own worked example, with isDisabled, resolveTimestamp and resolveUserGroupRestriction — has to check _system first and fall back to the raw field.

What the server answered instead: typo3_hint_lookup returned persistence-reading, frontend-access-restriction and frontend-records. persistence-reading is excellent on the adjacent question — DefaultRestrictionContainer, removeAll(), FrontendRestrictionContainer, versionOL, getLanguageOverlay — but it is entirely about which rows a QUERY returns. This is about what the resulting object's array looks like afterwards, which is one layer further on and the opposite failure: persistence-reading covers the row that is missing from the result, this covers the row that is there with a field silently gone.

typo3_documentation_lookup with "Record API system properties" at 14.3 returned nothing on it either — the hits were addRecord (TCA field control), System categories and System registry, all matched on the words rather than the subject.

## Query

typo3_hint_lookup(task: "Record API SystemProperties hidden starttime endtime fe_group enable fields"); typo3_documentation_lookup(queries: ["Record API system properties"], targetVersion: "14")

## Suggestion

Add a hint for the Record API's object shape — record-system-properties, or a section inside persistence-reading, which already ends where this begins and would make the pairing obvious.

The three things it has to carry, in this order: that RecordFactory unsets the enable and system fields from the properties rather than duplicating them, so the absence is by design; the _system key list with the types, because publishAt being a DateTimeInterface and userGroupRestriction being an int array are what a caller gets wrong next; and the two-shape problem with the fallback, since code shared between the page module and FormEngine is where this actually bites and RecordIdentityRenderer is the core's own answer to it.

Name the silent-failure shape explicitly. "$row['hidden'] is empty and empty means not disabled" is the whole finding, and a hint that lists the accessors without saying that will not stop anybody writing the bug.
