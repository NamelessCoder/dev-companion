# Say what the resource surface is for, and who it serves

**Serves:** requirements/
**Priority:** normal
**Waiting on:** does the resource surface serve the three audiences, or is it
    the core-contribution corpus it currently is — four of five documents
    core-only — and says so? The measuring is done and the metadata gap is
    settled either way; what the answer decides is which documents are offered
    at all, and a requirement written before it would hold the wrong shape.

Write the requirement that holds the resource surface, because there is none —
no file in `requirements/` names `typo3://` at all — and take the protocol's own
criterion as the thing it is measured against: resources are application-driven,
picked by the host or the user, where a tool is called by the model mid-task.
Three things are off against it. The six `ResourceDefinition`s built in
[`src/Server/Factory.php`](../../src/Server/Factory.php) set `uri`, `name`,
`title` and `mimeType` and leave `description`, `annotations` and `size` null
although the SDK carries all three, and `description` is the field the spec says
a client reads to understand what it is being offered. `annotations.priority`
and `annotations.lastModified` are free here and are what lets a picker sort.
And four of the five documents are core-only — `Documents::isCoreOnly` says so
for every one but `typo3-commit-messages` — so a surface serving one audience
sits beside a tool list serving three, and no decision records that. Resource
templates are not the gap: five documents make a six-entry list, and a
`typo3://core/{id}` template would hide them from a picker to save nothing.
