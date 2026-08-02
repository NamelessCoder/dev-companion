---
id: R-FBK-011
status: open
restsOn: [D-FBK-019]
---

# R-FBK-011 — A recorded feedback carries no secret out of the installation

**A session recording feedback is told that a value the installation keeps
secret does not belong in it.**

This server's only write moves text from the project a session is standing in
into a checkout of its own, and that checkout is committed and pushed. A key, a
password or a token pasted as evidence is copied out of one repository into
another, where the person who owns it is not looking and cannot take it back.

What the report needs is the path and the shape of the answer, never the value:
"the key at `SYS/encryptionKey` is the active one, hardcoded in
`config/system/settings.php`" is the whole finding, and the 96 characters after
it establish nothing further.

## From

`feedback/archive/2026-07-31-185900-after-the-audit-i-invoked-typo3-cms-mcp.md`
(2026-07-31), which quoted the live encryption key of the audited site twice —
once in its observation and once in its query — while reporting that
`typo3_configuration_lookup` had worked.

## Held by

Not guarded. Nothing reads what a session writes into `observation`, and no
check reads the corpus for what a value out of an installation looks like.
