# Document the resource surface the way the tools are documented

**Serves:** R-ANS-022, documentation/
**Priority:** normal

Every one of the 26 tools has a page in `documentation/tools/` carrying its
description, its schema and a recorded answer, held by `bin/cli tools:check` and
written by `tools:index`. The resource surface has four lines in `readme.md`
naming `typo3://core` and `typo3://core/{documentId}`, and nothing else anywhere
— which is the same gap `R-ANS-022` closed on the requirements side, where no
file had named `typo3://` at all. Write the page that says what a resource is
here: that it is picked by the host or the user rather than called by the model
mid-task, which is what the protocol distinguishes and what decides everything
else about it; what each family is and who it serves; what `description`,
`priority` and `size` carry, including that `priority` means only the ordering
and that `annotations.audience` is the protocol's `user`/`assistant` and never
the three audiences of `R-AUD-001`; and that `lastModified` is in the spec but
absent from `Mcp\Schema\Annotations` at the SDK version in `composer.lock`.
Decide in the same page whether it is generated from `Factory::resources()` the
way the tool pages are — a generated page cannot go stale, and a handful of
entries may not earn a generator — and where it says "generated", building that
is a second todo rather than this one. Wait for the skills family to land first:
it is being built now, the count of entries is not settled while the question of
what a skill's `references/` become is open, and a page written before that is
wrong on the day after it.
