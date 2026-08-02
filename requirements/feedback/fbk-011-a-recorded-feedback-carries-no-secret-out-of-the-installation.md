---
id: R-FBK-011
status: held
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
(2026-07-31), which pasted the live encryption key of the audited site into its
observation while reporting that `typo3_configuration_lookup` had worked.

This entry and `D-FBK-019` both said the key was quoted twice, once in the
observation and once in the query. The file says once. Its query names the
argument and `config/system/settings.php:118` without the value, and the commit
that first recorded it, `77d242b`, says the same — so the second field is a
place the value could go rather than one it went.

## Held by

The `observation` and `query` descriptions in `src/Tool/FeedbackRecord.php` say
what a finding needs — the path, the shape, where the value came from — and that
a value the installation keeps secret is not part of it. Telling the session is
the whole of what carries this, and whether it is heard is not guarded: nothing
reads what a session writes into either field, and no check reads the corpus for
what a value out of an installation looks like.
