---
date: 2026-07-29T18:05:28+00:00
category: idea
status: open
tool: typo3_architecture_lookup, typo3_task_guide
directory: /home/benji/projects/site-new
---

# TWO ANSWER SHAPES FOR ONE SUBJECT, SELECTED BY WHO IS ASKING

## Observation

*Trimmed after the repository level was answered: `project-repository-layout`
now covers what sits around the extension — the two units and the test that
tells them apart, Build/, the scripts, config/sites/ against the shipped
`Initialisation/Site/`, var/, and the built-assets decision. What follows is the
half of the original note that is still open.*

The catalog is organised by subsystem — fluid-templates, tca-formengine,
icon-usage, caching. That is a core contributor's model: "I am touching
FormEngine, what are the rules." A project developer never asks that. The
question is always "where does this go", and the hints that carried the session
are the ones that cut across subsystems to answer a project situation instead.

`core-tests` and `project-extension-tests` are the shape that works: one subject,
two answers, because the conventions are the same and everything around them —
the harness, the paths, what is available at all — is not. That pair exists
because a note asked for it, not because anything makes it the rule.

The general form: where a subsystem hint would read differently for a project,
that is the signal it needs its project-shaped twin, and the `outsideCore` flag
the server already computes is what would select between them. Whether that is
worth a mechanism or stays a rule for whoever writes the next hint is the open
question — a mechanism needs an audience field per hint, and the evidence for
one is currently a single pair.

## Query

synthesis of the session — which questions the knowledge base answers well for a
project, and which shape of question it has no place for
