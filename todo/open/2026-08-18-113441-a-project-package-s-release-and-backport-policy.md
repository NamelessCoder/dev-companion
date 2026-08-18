# a project package's release and backport policy had to come from the human after work had already...

**Serves:** feedback/2026-08-18-113441-a-project-package-s-release-and-backport-policy.md
**Priority:** normal

Write the rule into `skills/typo3-extension-cleanup/SKILL.md`, at the step where
a change is committed: which branch a fix belongs on, whether it is squashed,
and which released lines it is carried to are the repository's policy, derivable
from nothing this server reads, so they are asked before a branch is pushed or a
pull request is opened. What the core does — a patch on the main branch,
cherry-picked down — is the core's own process and is not the default anywhere
else. Say what the answer is worth keeping in: the maintainer states it once and
the session works from it, rather than re-asking per fix.

Judged on 2026-08-19 as the ladder's step 1a and written up in `D-FBK-050`. The
half of the feedback asking `typo3_project_describe` to infer the policy from a
`BP_*` branch scheme paired with tags is declined there: the session ran those
git commands, and what they returned is what nearly sent a fix to `BP_15_0`,
which is not supported. So this is a rule and not a field.

Two things the writing has to settle, neither of which needed a reading of TYPO3
and so neither is queued behind an answer. Whether the sentence belongs in the
cleanup skill alone or also at the point the draft
`typo3-extension-patch-review` hands over — a copy in both is one that cannot be
corrected, which is the skills' own rule. And whether `D-SKL-035` asks a
baseline run of a rule added to a published skill or only of a new one.

The feedback stays open until the commit that writes the rule archives it with
`bin/cli feedback:archive`.
