# How the work is done

One page per procedure that is long enough to get wrong from memory. The
directories keep saying what a thing **is** — a requirement, a decision, a
forward review, a note — and each links here for how it is carried out.

The split is deliberate. A readme that also holds the workflow is read by
someone looking for one paragraph and finding six; a workflow with no home ends
up in an agent's private memory, where nobody else can read it and no checkout
carries it. Both happened here before this directory existed.

| Page | What it carries |
| --- | --- |
| [feedback.md](feedback.md) | How work moves between `feedback/`, `requirements/`, `decisions/` and `todo.md`, what `bin/cli next` calls due, and the debrief that gets notes out of a session this repository cannot read |
| [installation.md](installation.md) | Every client and layout the installer supports, and the configuration each one gets |
| [working-on-the-server.md](working-on-the-server.md) | The commands this repository is kept in order by, the core checkouts, the test suite |
| [forward-runs.md](forward-runs.md) | Running a forward review, judging it, and what to do when one stops without an error |
| [writing-a-skill.md](writing-a-skill.md) | What a new task skill has to show before it exists, the rules it is written under, and what holds each one |
| [knowledge-versions.md](knowledge-versions.md) | Writing a statement that holds for some TYPO3 versions and not others |
| [asking-the-installation.md](asking-the-installation.md) | The order an installation's own answers are looked up in, and what a fallback owes the caller |

What does **not** belong here: what must hold — that is
[requirements/](../requirements/readme.md); what a change assumed —
[decisions/](../decisions/readme.md); the order of the work —
[todo.md](../todo.md), which is also where this machine's checkouts are named;
and the conventions every session works under, which stay in
[AGENTS.md](../AGENTS.md) because they are read before anything else is.
