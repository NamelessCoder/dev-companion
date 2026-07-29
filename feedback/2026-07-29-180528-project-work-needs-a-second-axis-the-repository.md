---
date: 2026-07-29T18:05:28+00:00
category: idea
status: open
tool: typo3_architecture_lookup, typo3_task_guide
directory: /home/benji/projects/site-new
---

# WHICH SUBSYSTEM HINTS STILL NEED A PROJECT-SHAPED TWIN

## Observation

*Trimmed twice. The repository level is answered by `project-repository-layout`,
and the force of a statement is now data: `binding: "core"` marks what is a
condition of a core patch and a convention elsewhere, on the hint for a whole
subject and on the statement for a single sentence. What is left of the note is
the part that marking cannot do.*

The catalog is organised by subsystem — fluid-templates, tca-formengine,
icon-usage, caching. That is a core contributor's model: "I am touching
FormEngine, what are the rules." A project developer never asks that. The
question is always "where does this go".

Marking answers "is this mine to follow". It does not answer "then what is mine
to do instead", and that is what a twin is for: `project-extension-tests` exists
because the conventions of `core-tests` transfer and everything around them —
the harness, the paths, what is available at all — does not. Two pairs exist
now, both because a note asked for one.

Open: go through the hints that carry `binding: "core"` and decide, per subject,
whether marking is enough or whether the project side is a real answer that is
missing. `documentation-changelog` is the clearest candidate — a project does
release notes too, and the hint currently says nothing about that. The backend
CSS hints are probably the opposite case: a project styling a backend module
wants the core's rules unchanged, and a twin would say the same thing twice.

## Query

synthesis of the session — which questions the knowledge base answers well for a
project, and which shape of question it has no place for
