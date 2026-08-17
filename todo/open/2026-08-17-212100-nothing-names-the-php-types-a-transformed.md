# Name the PHP types a record and its transformed columns arrive as

**Serves:** feedback/2026-08-17-212100-nothing-names-the-php-types-a-transformed.md
**Priority:** normal

Step 1a on `D-KNW-090`: the corpus says how a field is read off `{record}` and
never what it is, so a typed `f:argument` has to be guessed at and fails at
render time. Establish it and write it onto `preview-record-variable` — read
`RecordFieldTransformer` in `.checkouts/14.3` for the columns that become value
objects rather than scalars, `TypolinkParameter` for `type=link` among them, and
name `TYPO3\CMS\Core\Domain\RecordInterface` as what the record itself is. The
`f:argument` half needs an installation's `vendor/typo3fluid/fluid` at the 5.3.1
that 14.3 pins, because no core checkout carries that ViewHelper; settle there
whether a backend preview transforms a link column the same way a frontend
partial does, which `D-KNW-090` assumes and the reporting session could not
verify.
