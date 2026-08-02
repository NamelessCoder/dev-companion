# Hold every `check` command in `D-EVI-3` to what it may not do

**Serves:** decisions/
**Branch:** todo/hold-every-check-command-in-d-evi-3-to-what-it-may-not
**Claimed:** 2026-08-02

A review runs the checks that cannot change the code, and the entry is wrong if
a run reports a checkout modified by a command classified `check`. The
classification lives in the skills, so it is readable here: list every command
any skill declares as `check` and confirm none of them writes. What would hold
it is a `SkillTest` assertion over that declared set against a list of
known-writing commands, so a skill that classifies a fixer as a check fails
before a run finds out the expensive way.
