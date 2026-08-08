# Route the core review's documentation surface to the manual

**Serves:** feedback/2026-08-08-224516-a-skill-driven-session-never-reaches-typo3.md
**Priority:** normal

Establish first what a core patch owes the manual, because
`knowledge/documents/core/contribution/rules.md` says nothing about it and the
answer decides whether the surface routes to a lookup or also to a rule that has
to be written: read the contribution documentation at docs.typo3.org and the
`Documentation/` directory of `.checkouts/main`. Then name
`typo3_documentation_lookup` where a review disposes of that surface — in the
**Documentation and changelog** surface of
`skills/typo3-core-patch-review/references/checklist.md`, which today lists only
changelog obligations, and at the "What the patch owes, per finding" step of its
`SKILL.md`, which routes to `typo3_rule_lookup` and `typo3_changelog_lookup` and
to no third owner — with the `ROUTING_SKILLS` entry in `SkillTest` and the
requirement that holds it. Name the four manuals `src/Manual/Documentation.php`
searches in `DocumentationLookup::description()` in the same work, because a
surface cannot be judged from a description that names no book. `D-SKL-030` is
the judgement, including why `typo3_server_scope` does not go into
`skills/base.md`.
