---
date: 2026-08-11T05:52:57+00:00
category: idea
status: closed
closed: 2026-08-12
model: claude-opus-5
tool: typo3_icon_lookup
directory: /home/benji/projects/typo3-cms
---

# Validating three known icon identifiers cost three round trips; the tool takes one query and answ...

## Observation

Task: while finishing Gerrit change 94686 the user asked for breadcrumb nodes to carry "the same icons used on the buttons in the overview". I read the three identifiers straight out of the Fluid template (Overview.fluid.html: actions-system-options-view for the detail route, actions-open for edit, actions-cog for editSettings, actions-plus for add) and then only needed to confirm each exists in this installation's registry before emitting it, as the server's own instructions require.

That was three separate calls, one per identifier, each a round trip. Each answered `exactMatch: true` and then spent most of the payload on things I had not asked for: "actions-open" came back with 22 suggestions and five near-matches (actions-document-history-open, actions-envelope-open-text, …), plus the same `scope` paragraph about IconFactory and the backend ViewHelper repeated in every one of the four answers.

The one exploratory call, query "site", was the useful shape — I did not know what existed and the ranked list plus the module-vs-modulegroup aliasing told me module-sites is the module icon and module-site the singular. That mode is right. The other three were pure existence checks and the tool has no shape for them.

## Query

typo3_icon_lookup(query "site", limit 25), then typo3_icon_lookup(query "actions-open", limit 6), typo3_icon_lookup(query "actions-cog", limit 3), typo3_icon_lookup(query "actions-plus", limit 3).

## Suggestion

Accept several identifiers in one call — either a list parameter or a comma-separated query — and when every term is an exact identifier, answer per identifier with exists / category / aliasOf and drop the suggestion ranking and the repeated `scope` paragraph. The two uses are genuinely different: "what icon means X" wants ranking, "do these three exist" wants a yes/no per name in one round trip. The instruction to validate every icon before emitting it makes the second the common case for any change that touches more than one icon.
