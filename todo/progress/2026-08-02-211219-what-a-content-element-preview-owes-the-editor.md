# What a content-element preview owes the editor

**Serves:** feedback/2026-08-01-003935-guidance-item-previews-for-content-elements.md
**Priority:** normal
**Branch:** todo/what-a-content-element-preview-owes-the-editor
**Claimed:** 2026-08-03

Step 1a of the ladder, on the evidence in
[`D-KNW-025`](../../decisions/knowledge/knw-025-what-a-backend-preview-owes-the-editor-is-a-gap-this-server-owns.md):
the corpus says where a preview is registered, what it is handed and which parts
not to repeat, and nothing says what belongs in it. Read all nine templates in
`.checkouts/14.3/typo3/sysext/theme_camino/Resources/Private/Templates/ContentPreviews/`
for what the core's own previews draw — which columns, which relations, where
they crop, and what they leave to the header. Then write that as one statement
beside the existing preview statements on the `content-elements` hint in
`knowledge/hints/content-elements.json`, naming the field kinds a preview draws
from rather than asking for a summary, with a requirement for what has to keep
holding. Settle the version binding while reading: `theme_camino` ships on 14
only and 13.4 has no worked preview, so the evidence is one major and the rule
may not be.
