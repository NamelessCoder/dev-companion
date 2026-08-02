# Say what shipping ext_tables.php costs, from the version it started costing it

**Serves:** feedback/2026-07-31-172757-the-two-newest-typo3-v14-3-extension.md
**Priority:** normal
**Branch:** todo/say-what-shipping-ext-tables-php-costs-from-14-3
**Claimed:** 2026-08-02

Judged as step 1a on the ladder, in `D-ANS-009`: `#108345` is written whole in
the `extension-files` hint of `knowledge/architecture-hints/php.json` and
`#109438` is in `knowledge/` nowhere, so the delivery half the feedback asks for
has no statement to deliver. Write that statement into `extension-files`, from
`Configuration\Extension\ExtTablesFactory` in `.checkouts/14.3` — every active
package that ships the file and is not `isFrameworkType()` raises
`E_USER_DEPRECATED` on cache warm-up and on an uncached request, and v15.0
removes the loading — and settle first how it binds, because `since` and `until`
carry major integers across all of `knowledge/` while this holds from 14.3 and
`HintsTest::noHintStatesSomethingThatOnlyHoldsOnOneBranch` forbids the minor in
the text. The delivery half — what `typo3_extension_scope` says about the two
files it already lists — is the todo after this one and is what archives the
feedback.
