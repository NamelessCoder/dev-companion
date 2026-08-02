# Settle whether a source that could not be asked has one word or two

**Serves:** R-ANS-001, decisions/

Two tools say "this source could not be asked" in two vocabularies. The
installation-backed eight answer with an `unsupported` object that replaces the
result and names a `cause`, which is `D-ANS-005`. `typo3_documentation_lookup`
carries `status` instead, whose cases are `answered`, `empty` and `unavailable`
— the same three states in one field, reached by a source that is
`docs.typo3.org` rather than an installation. The step is to settle which is the
one word, in a decision: whether the manual keeps `status` because its
reachability is a different axis from an installation's, or whether both go to
one shape and a caller reads one model rather than two. Read
`Manual/Documentation.php` and `Result/Schema.php` against each other, decide,
and write the entry — this is `D-KNW-005` one directory over, and that one
settled four spellings into `scope`.
