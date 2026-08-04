# Store a recorded name in the case it was given in

**Serves:** feedback/2026-08-04-180241-task-filing-session-feedback-at-the-end-of-a.md, R-FBK-013
**Priority:** normal

`R-FBK-013` reads `held` and does not hold for a name carrying capitals:
`Channel::toolNames()` at `src/Feedback/Channel.php:719` lowercases what it
keeps, so `ListMcpResourcesTool` is stored as `listmcpresourcestool`. Take the
`strtolower()` out of the writer and put it into `Channel::comparable()`, which
strips everything outside `[a-z0-9]` today and would drop the capitals of a name
stored correctly rather than fold them — the filter has to find one spelling
from the other in both directions. Then sweep the corpus the way `D-FBK-039`
did: the stored names that resolve to no tool and no skill are read against the
registry and `skills/`, and what does not resolve stays as it was written, which
is where a client's own tool name belongs. Add the mixed-case case to
`FeedbackTest::aRecordedNameKeepsTheSpellingItWasGivenIn` and to
`aNameIsFoundHoweverItsSeparatorsAreSpelled`, since both passed while this was
wrong.
