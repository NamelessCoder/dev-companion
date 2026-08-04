# Say what a named check has to be established about

**Serves:** feedback/2026-08-04-175840-task-establish-a-complete-check-layer-for-a.md, feedback/2026-08-04-175856-task-establish-a-check-layer-for-a-typo3-14-3-5.md, feedback/2026-08-04-180154-task-establish-a-check-layer-then-after-the.md, skills/typo3-extension-testing/
**Priority:** normal

`D-SKL-017` decided that
`skills/typo3-extension-testing/references/static-quality.md` names the check
and says what a session establishes about it on the package in hand. The
TypoScript measurement is done and came out against the feedback: on 2026-08-04
`helmich/typo3-typoscript-lint` v3.3.0 resolved cleanly against
`.environments/e-site-14.3` — TYPO3 14.3.5, `symfony/event-dispatcher` v7.4.15 —
in a `--dry-run` require. `typo3/cms-core` 14.3.5 requires no
`symfony/event-dispatcher` at all, only `event-dispatcher-contracts ^3.6.0`, so
the reporting project's 8.1.2 came from elsewhere: `composer why` in
`/home/benji/projects/site-new` names `friendsofphp/php-cs-fixer` and
`symfony/mailer`, both of which accept `^8.0`. So what the tool cannot meet is
what the installation resolved, never the TYPO3 version, and an unpinned
transitive package can sit a major ahead of what the core asks for.

What is left is the other two measurements and then one rewrite of the page. Run
`symfony/translation`'s `XliffLintCommand` over locale-prefixed
`de.messages.xlf` files with `requireStrictFileNames` at its default and at
`false` — the flag is a constructor argument, so a short script rather than a
console call — and run the named frontend linters over a package that ships one
script and a stylesheet. Then rewrite the two rows and "Establish one command
per check": the XLIFF answer named, resolve-before-naming with the isolated tool
install as the option where a tool's constraints cannot meet what the project
resolved, and measure-what-it-finds before a row is called covered. What the
measurements find about a version, a constructor default or a sniff's advice
goes to `knowledge/` rather than into the published page — the constants-era
advice of `DuplicateAssignment` and `RepeatingRValue` and the locale-prefix trap
are statements that carry a `since`.
