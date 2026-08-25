# breaking-not-assessed calls a widened protected member breaking; the core's own history does not

**Serves:** feedback/2026-08-24-183209-breaking-not-assessed-calls-a-widened-protected.md, D-KNW-122
**Priority:** normal

Judged step 4 and step 1a in `D-KNW-122`, which holds the sweep so it is not run
again: the core promotes a member from protected to public in plain commits with
no changelog file, and three of them reach a maintained release line. Rewrite
the `breaking-not-assessed` message in `src/Knowledge/CommitMessage.php` so
"widened" names the signature it was written for and not the visibility, keeping
it to which member kinds a caller enumerates, and state what a widened
visibility does owe in `## Changed Signatures` of
`knowledge/documents/core/contribution/commit-messages.md` — where the sentence
"adding a parameter is one" already stops short of it.
