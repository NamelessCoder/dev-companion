# Name the skill resources in the comment on the uniform scheme

**Serves:** decisions/scope/
**Priority:** normal

`D-SCO-010` is the entry this answers for, and the file is
[the drafted comment](../../documentation/interface-contract/comment-on-the-uniform-scheme.md).
Its section "What already occupies `typo3://`" lists two shapes — `typo3://core`
and `typo3://core/<id>` — and this server now serves two more:
`typo3://skill/<id>/SKILL.md` for each published task workflow and
`typo3://skill/<id>/references/<file>` as a resource template for what a
workflow hands over at a step. Add them there, and reread the paragraph under
the list while doing it: the argument is that the scheme is not an
implementation detail this server could quietly change, and the skills make it
stronger rather than different, because a published skill's own links resolve
against the URI its body is served at. The comment has not been filed yet —
`todo/open/2026-08-04-104500` is the card that files it and reads the RFC's
comment period first — so this is a correction to a draft rather than to
something somebody has read, and it belongs in whichever of the two commits
comes first.
