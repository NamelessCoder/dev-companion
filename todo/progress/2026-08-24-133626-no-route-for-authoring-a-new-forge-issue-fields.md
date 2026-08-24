# Write the document a new core issue is reported from

**Serves:** feedback/2026-08-24-133626-no-route-for-authoring-a-new-forge-issue-fields.md
**Priority:** normal
**Branch:** todo/no-route-for-authoring-a-new-forge-issue-fields
**Claimed:** 2026-08-24

Judged on 2026-08-24 as `D-KNW-113`: step 1a for the fields, the target-version
convention and the markup, step 2 for the areas, and taken on. What the session
could not get is the shape of a report rather than one statement, so the answer
is a document.

Read the new-issue form of the core project at forge.typo3.org and establish
what it takes: which fields a Bug carries, which of them are mandatory, what a
bugfix sets Target version to, and whether the description renders Textile or
Markdown with the code-block syntax that goes with it. Then write
`knowledge/documents/core/contribution/reporting-an-issue.md` from it, naming
the `typo3_forge_lookup` call that reads the areas rather than copying their
names, and route to it from `knowledge/task-intents.json` and the `routing` of
`knowledge/server-scope.json`.

Two things go with it. `typo3_forge_lookup` answers the areas only to a
`category` word that names none, so the route to them without a wrong word is
part of this; and the `categories` description in `ForgeLookup::outputSchema()`
says `typo3_server_scope` carries that vocabulary, which it does not.
