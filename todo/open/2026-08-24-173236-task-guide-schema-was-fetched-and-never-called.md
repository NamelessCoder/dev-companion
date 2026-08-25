# A brief naming the tracker by its name routes to no skill

**Serves:** feedback/2026-08-24-173236-task-guide-schema-was-fetched-and-never-called.md
**Priority:** normal

Judged on 2026-08-25 as the ladder's step 3, and the evidence is in the
`2026-08-25` section of
[D-SKL-023](../../decisions/task-skills/skl-023-a-skill-no-intent-names-is-one-the-brief-cannot-route-to.md):
"fetch another old issue from Forge, create a branch, work it off" matches the
triage intent weakly and answers `skills: []`, so the call this feedback says it
should have made would have named no skill.

Read `forge` as the tracker's name on both gates that decide a core-scoped
intent. `Scope::CORE_WORK` carries it as `'forge '` and is matched with
`str_contains`, so the comma in "from Forge," sits where the trailing space has
to be; `Text::containsWord` is what the intent matcher already uses for the same
job and keeps "forget" out by the rule. Then decide which needles the `triage`
entry in `knowledge/task-intents.json` takes — bare `forge` in `match`, or the
phrases around it, of which "forge issue" is already `reporting`'s — on what
else starts matching. The failing case is the sentence above, held in
`SkillTest` beside `aBacklogSearchMatchesTheSkillThatOwnsTheCandidates`.
