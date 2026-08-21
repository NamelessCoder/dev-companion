# A release audit turns on what is already published in the TER, and nothing here can see it

**Serves:** feedback/2026-08-19-094528-a-release-audit-turns-on-what-is-already.md
**Priority:** normal

Build `typo3_ter_lookup`: an extension key goes in, and what the TER holds under
it comes out, one entry per published version with the number, the state, the
upload date, the `typo3_versions` majors and the `dependencies.typo3` constraint
that version declared. It reads
`https://extensions.typo3.org/api/v1/extension/<key>/versions` through
`Http\Fetch`, which is the one way this server reads a host outside itself, and
it carries `openWorldHint` like the other three that do. A key the TER does not
know answers `404` with a JSON body, and that is a miss with the same shape as a
hit rather than a failure. Settle the fields against the API rather than against
this paragraph — `D-FBK-051` names what one response carried on 2026-08-21 — and
add the tool to `covers` and to `routing` in `knowledge/server-scope.json`,
where the `doesNotCover` entry about someone else's extension also needs the
boundary said: the registry's published record for a key is answered, the inside
of that package is not. Extend `extension-ter-release` in
`knowledge/hints/extension.json` in the same change, with what makes the audit
turn: the TER refuses a version it already holds, and `ext_emconf.php` keeps
naming the released version afterwards, so a published repository and an
unreleased one read alike. Verify both against Tailor and the TER before writing
them.

Judged on 2026-08-21 as the ladder's step 1b, with the wording half at 1a, and
written up in `D-FBK-051`. A field on `typo3_extension_describe` is declined
there — that tool misses where the installation does not have the extension,
which is exactly where a release audit stands — and so is routing the caller to
the publish workflow's run history, which is git and CI state and answers a
different question.

The feedback stays open until the commit that ships the tool archives it with
`bin/cli feedback:archive`.
