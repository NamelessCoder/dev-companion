# Verify a rule quoted from a tracker against the checkout

**Serves:** feedback/2026-08-02-144814-task-assess-forge-105403-and-fix-it-a-core.md
**Priority:** normal

Judged on 2026-08-03 and trimmed to the half that is left. Steps 1a and 4
together, on the evidence in
[`D-KNW-043`](../../decisions/knowledge/knw-043-a-rule-about-what-an-api-may-be-used-for-carries-its-strength-and-its-source.md):
the corpus repeated the maintainer's "must not" by flattening `f:image` and
`f:uri.image` into one documented rule, which is corrected in that commit, and
the probe that reached nothing now reaches `fluid-resource-uris` first. What is
left is the feedback's third suggestion — that a rule quoted from a tracker
comment or from prose documentation is a claim to verify against the checkout,
the way a path and an identifier already are. That orders a task rather than
stating a fact, so it is one step of the core-contribution **creation** order
under
[`D-SKL-005`](../../decisions/task-skills/skl-005-core-contribution-earns-a-skill-and-the-domain-is-the-work.md),
whose assessment half comes from this same cluster: write it there, next to the
steps `feedback/2026-08-02-145128` and `144800` carry, rather than as an order
of its own. `normal` rather than `low` because two sessions of the cluster
reported an assessment formed on a maintainer's statement before any code was
read, and one of them committed a patch on it. If the card that writes that
order lands first, this one is absorbed by it and deleted; nothing here needs
doing twice.
