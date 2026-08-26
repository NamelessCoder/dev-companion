# Say in the commit guide's answer where the 72-character boundary runs

**Serves:** feedback/2026-08-24-205132-the-guide-wraps-bodies-at-exactly-72-characters.md, D-GUI-020
**Priority:** normal

Judged on 2026-08-26 as step 4 of the ladder, wording: the boundary is here and
the answer never states it. Make `CommitMessage::checks()` say, under
`workflow="core"` and on a draft with no `body-line-too-long`, that 72
characters is the longest line `Build/git-hooks/commit-msg` accepts and 73 the
first it refuses — the width alone does not settle a checkout whose `AGENTS.md`
is one stricter. Which code that check carries, whether the `CommitMessageGuide`
description and `body-line-too-long` say it too, and what holds it in
`CommitMessageGuideTest` are the shape to decide; the boundary itself is
measured in `D-GUI-020` and needs no further reading.
