---
id: R-SKL-016
status: held
restsOn: [D-SKL-010]
---

# R-SKL-016 — The assessment before a core patch reads the issue and the review server

**A core patch is assessed before it is written, and that assessment asks the
tracker for the issue and the review server for whether somebody has already
fixed it.**

Neither answer is in the checkout, and neither is in the report a session starts
from. What the tracker adds is the status and the target version as they stand
today, the relations — which are one hop from the change that introduced the
behaviour being complained about, where a query on the wording misses it — and
the notes, which is where a closure, a reassignment and a maintainer's reason
live. The review server answers "has somebody already fixed this" for one call,
and it is asked before any code is written because the outcome that cancels the
work is worth nothing once the work is done. It is read anonymously, so an
answer of nothing says that nothing public names the issue rather than that
nobody has fixed it.

Three rungs go with the two calls, because each is a judgement the assessment
makes and the order carried none of them. A deferred decision is checked against
what the branch has today before its blocker counts as standing. The argument
that carries a bugfix is the same inconsistency inside one version, which is
what separates a defect from a wish. The blast radius is established while
assessing, because it decides the change type and everything downstream of it.

This is [R-SKL-014](skl-014-a-review-reads-what-the-project-already-says-about-the-patch.md)
on the other side of the same work: the review reads both surfaces by the
numbers the commit message carries, and the session about to write the patch has
only the issue number and is the one that can still be spared the work.

## From

Five sessions of one cluster, all on Forge #105403 (2026-08-02). Four ran both
lookups by hand — `feedback/2026-08-02-144511`, `144848`, `145217`, `145230` —
and the fifth, `feedback/2026-08-02-145128`, filed the assessment method it had
had to rediscover, of which the three rungs are the part no order here carried.
That session treated a 2024 objection as standing after the API it rested on had
arrived, characterised the change before finding that it moved about 141
expectations across 23 files, and reached the change that introduced the
behaviour by searching the tracker on the feature wording rather than through
the reported issue's own relations.

## Held by

- `SkillTest::theAssessmentBeforeAPatchReadsTheIssueAndTheReviewServer`
