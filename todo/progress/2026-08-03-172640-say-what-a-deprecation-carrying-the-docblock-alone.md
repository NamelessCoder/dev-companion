# Say what a deprecation carrying the docblock alone raises

**Serves:** feedback/2026-08-03-164805-installation-there-is-no-cheap-way-to-ask-is.md
**Priority:** normal
**Branch:** todo/say-what-a-deprecation-carrying-the-docblock-alone
**Claimed:** 2026-08-03

`deprecated-apis` in `knowledge/hints/upgrade.json` states the marking as a pair
— "an @deprecated annotation together with a trigger_error(...,
E_USER_DEPRECATED) call is what marks one" — and has no word for the docblock
standing alone, which is what a deprecated class constant carries. Write that
case into the hint:
`.checkouts/14.3/typo3/sysext/fluid/Classes/ViewHelpers/Be/InfoboxViewHelper.php:59`
deprecates `STATE_ERROR` in a docblock and the file has no `trigger_error` at
all, so nothing is raised today and the call site breaks at v15 — the severity
`feedback/2026-08-03-164805` went to the source for. Establish first what makes
a reading of `@deprecated` alone conclusive, since a method can raise from a
caller rather than from its own body. Bring the measurement with it —
`#[\Deprecated]` occurs zero times in `typo3/sysext` on `.checkouts/12.4`,
`13.4`, `14.3` and `main`, so the PHP attribute is not how the core marks one —
and check the hint's opening sentence while there: "this server does not know
your branch" is false where an installation answers, which is where
`typo3_changelog_lookup` reads its changelog from. `D-ANS-010` carries the
readings.
