# Point what still cites `D-DIS-008` at the successor that holds

**Serves:** R-DIS-010, src/Installation/
**Priority:** low

`D-DIS-008` is revoked and
[`D-DIS-012`](../../decisions/discovery/dis-012-the-driver-decides-whether-the-derived-columns-need-the-database-server.md)
is what holds: the condition on the derived columns is the driver's, and the
description, the tool page and `probe.php`'s comment say so. Two places still
name the revoked entry for that reading — `Typo3Cli::caveat()`'s docblock, in
the sentence about `pdo_sqlite` and `pdo_mysql`, and the **From** section of
`R-DIS-010`, in the same sentence. Both already state the corrected reading, so
the citation is what is stale rather than the prose: swap the id in each, read
the surrounding paragraph for whether anything else there was written against
the old statement, and leave `SchemaLookup`'s class docblock alone — its
`D-DIS-008` names the bullet that bounds the tool to the derived side, which the
revocation did not touch.

`low`, because a revoked entry carries `revokedBy` and the generated listing
shows it, so a reader following either citation lands on the successor anyway.
Both files were held by another session while this was worked, which is why they
are not done here.
