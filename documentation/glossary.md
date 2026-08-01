# Glossary

One thing, one word. Every word here is one this repository already writes;
where two compete for the same thing, the one that wins is the one somebody
outside this checkout can see — a tool name, a directory name, a CLI subject.

`feedback` is countable here: **a feedback** is one report, one file, one
subject, and it arrives in the **feedback channel**.

## What is written down here

| Word | What it is |
| --- | --- |
| **a feedback** | one report about this server, left by a session working elsewhere |
| **the feedback channel** | where they arrive and wait: `feedback/`, written by `typo3_feedback_record` |
| **requirement** | what must be true from now on, in `requirements/` |
| **decision** | what a change rested on, and what would show it wrong, in `decisions/` |
| **todo** | one entry in the order of the work |

## What happens to a feedback

| Word | What it is |
| --- | --- |
| **record** | the verb a feedback comes into being by, and the tool verb for it |
| **judge** | to work out what should become of one open feedback, on evidence |
| **answer** | what became of it: the change, and the commit that made it |
| **archive** | to move it to `feedback/archive/` once it has an answer |

## What the server ships

| Word | What it is |
| --- | --- |
| **knowledge** | everything below `knowledge/`: what the tools answer from |
| **hint** | one statement in `knowledge/architecture-hints/`, reached by `bin/cli hints probe` |
| **catalog** | the component catalog in `knowledge/catalog/` |
| **skill** | one canonical task workflow under `skills/`, installed into somebody else's project |
| **tool** | one `typo3_<subject>_<verb>` the server offers |
| **lookup, guide, list, scope, record** | the five tool verbs, each naming the shape of the answer |

## What is measured

| Word | What it is |
| --- | --- |
| **scenario** | one prompt and what has to come out of it, in `scenarios/` |
| **forward review** | an open scenario in `scenarios/forward/` that names no subsystem |
| **contract case** | a targeted scenario in `scenarios/contracts/` naming one task shape |
| **run** | one recorded execution of a forward review, in `scenarios/runs/` |
| **verdict** | how a run came out: `covered`, `partial`, `gap` — derived, never written |

## The states

None of these may make a check fail.

| Word | Where | Means |
| --- | --- | --- |
| **open** / **closed** | a feedback | which directory it is in |
| **held** | requirement | something holds it to being true, and the entry names what |
| **not guarded** | requirement | nothing can hold it, written down as such |
| **standing** | decision | nobody has been back to its **Wrong if** |
| **corrected** | decision | it was wrong, and the entry says so in place |
| **tested** | decision | its **Wrong if** is now held by a test |

## Where the environment is

| Word | What it is |
| --- | --- |
| **checkout** | one TYPO3 core worktree below `.checkouts/`, created by `bin/cli checkouts update` |
| **installation** | the TYPO3 a calling agent is actually working in |
| **standalone checkout** | this repository as the Composer root package, where feedback can be written |

## Adding a word

A new word is added in the commit that first writes it, and only after looking
for the one it duplicates. A word that appears in a tool name, a directory name
or a CLI subject cannot be changed here without changing those first.
