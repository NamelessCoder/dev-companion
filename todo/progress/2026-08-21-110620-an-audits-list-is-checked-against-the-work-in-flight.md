# An audit's list is checked against the work already in flight

**Serves:** feedback/2026-08-19-094341-nothing-covers-checking-audit-findings-against.md, R-SKL-025, D-SKL-068
**Priority:** normal
**Branch:** todo/an-audits-list-is-checked-against-the-work-in-flight
**Claimed:** 2026-08-21

Judged as a gap in the shape rather than in the knowledge: the skill owns the
gate where a list is agreed and asks nothing about what the repository already
carries, and `D-SKL-068` has the evidence and the boundary. Establish the method
first, against a real repository with unmerged branches — whether a two-dot diff
restricted to the files a branch touches actually distinguishes a landed branch
from an outstanding one, on a squash-merged branch and on a rebased one, and
whether `gh` may be assumed at all — then write the step into
`skills/typo3-extension-health/SKILL.md` between step 5 and step 6, naming the
three surfaces `D-SKL-068` fixes and one state per item, and put the `SkillTest`
assertion that reads it into `R-SKL-025`'s **Held by**, which names none today.
