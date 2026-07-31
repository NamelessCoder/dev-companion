---
id: D-SKL-1
date: 2026-08-01
status: tested
---

# D-SKL-1 — The order a task starts in is one file, and the reading comes last in it

**`skills/base.md` holds the order every task starts in — project scope,
extension scope, task guide, conventions, and only then the checkout — and is
copied into each published skill rather than shared with it.**

Four recorded `REVIEW-01` runs of the same prompt in the same checkout took it
from a review this server took no part in to `covered`. The thing that decided
each step was never the wording of an instruction; it was where the reading of
the checkout sat relative to everything else.

- **Evidence:** run 2 activated the skill and followed two of its seven evidence
  steps. Run 3 read the skill's checklist in its first twenty seconds, then ran
  `find . -type f` and spent five minutes reading the result before calling
  `typo3_task_guide` or a single conventions lookup — and filed translations
  under "assessed and clean" with `source-language="de"` on screen, because it
  had asked `typo3_translation_domain_lookup` and taken a runtime answer for a
  verdict. Comparing the other four skills then showed the arrangement was
  everywhere: "inspect the checkout" stood at step 2 of 6 in content-element, 3
  of 6 in documentation, 3 of 7 in testing, each with the conventions lookup
  behind it. Run 4, against one base file, walked the order and produced both
  findings three runs had missed.
- **Decided:** `skills/base.md` holds the order — project scope, extension scope,
  task guide, conventions per subsystem, and only then the checkout — plus the
  two things no skill should re-derive: a runtime lookup reports what is
  registered and never whether it is right, and a returned rule judges the code
  that already exists as well as the code about to be written. `Installer` copies
  it into each published skill as `references/base.md` rather than sharing one
  file, because a skill lands in someone else's project alone. Each `SKILL.md`
  states only what it adds. Two tool defects found the same way were repaired
  rather than worked around: an identifier reaching `addRecordType()` through a
  variable was dropped silently, and nothing reported what an extension does not
  ship.
- **Wrong if:** `REVIEW-02` in an extension repository shows the base being
  outrun again, which would mean the order is followed only where a checklist
  reinforces it and the conformance skill was carrying it rather than the base.
  Or the base grows: it is load-bearing because it is short enough to be read
  before the first call, and every sentence added to it is one the reading can
  swallow. It is also unproven for four of the five skills — their order was
  corrected on the strength of a defect measured in the fifth, and no forward run
  has touched them.
- **Tested on 2026-07-31:** `REVIEW-02` ran in `bootstrap_package` and the base
  was not outrun. The skill activated on its own, `references/base.md` and the
  checklist were read three seconds later, the three scope calls followed, and
  the six-surface list was written out 22 seconds before the first project file
  was opened. So the order survives a checkout of another kind — by the same
  skill. The four whose order was corrected without a forward run are still
  unproven, and the base is still short.
