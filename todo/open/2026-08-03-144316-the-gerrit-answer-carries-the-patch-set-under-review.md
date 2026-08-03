# The Gerrit answer carries the patch set under review

**Serves:** feedback/2026-08-03-144316-this-is-what-worked-in-a-core-patch-review-and.md
**Priority:** normal

Judged on 2026-08-03 as a missing field rather than a missing tool, on the
reporting session's own evidence read against the answer: it credited the
Change-Id lookup with establishing that it held the patch set that is on the
server, and neither half of the answer says which patch set that is. Raised from
`low` because a review holding the wrong revision is wrong in every finding
after it, and because the session could not tell — the number it reported came
from the automated Gerrit note in the Forge answer. `D-FBK-018` has the reading.

Add the current patch set to `typo3_gerrit_lookup`: its revision number and
commit hash, in the text and in `changes[]` beside `status` and `branch`, so a
reviewer can hold it against its own `HEAD`. Gerrit returns neither by default.
The first step is establishing which query option carries them anonymously —
`o=CURRENT_REVISION`, over the same anonymous path `D-ANS-033` settled. The
schema comes after that, and after it the sentence the answer owes where a
checkout and the current patch set differ.
