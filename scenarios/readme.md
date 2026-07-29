# Test scenarios

What this server is worth is decided in a session it did not design: someone
gives an agent a task in their own words, the agent reaches for whatever tools
it has, and the answer is either right for that person or it is not. This
directory holds those sessions, written down so they can be run again.

Each scenario is a **prompt a user would actually type**, plus the environment
it has to be typed in, plus what has to come out of it. The prompt never names
a tool and never mentions this server — an agent that only finds the right tool
because the question spelled its name has established nothing. The task decides
the audience, the paths decide the scope, and whether the server recognises
that is exactly what is under test.

**Scenarios the server cannot answer yet belong here.** The suite is not a
regression net around what already works; it is the map of what the three
audiences need. A scenario marked `gap` is a scenario whose answer is still to
be built, and it stays in the suite with that mark until it is. Running one is
useful precisely because it produces the note that says what was missing.

## The three audiences

- **Core contributor** — works in a TYPO3 core checkout, patches go to Gerrit.
- **Extension author** — maintains an extension repository, often developed
  inside a site installation, released for several TYPO3 versions.
- **Site developer** — builds and maintains one installation: site
  configuration, site package, content, upgrades, deployment.

The same person is routinely two of them on the same day, which is why the
audience is a property of the task rather than of the directory
(`R-AUD-2` in [requirements.md](../requirements.md)).

## Files

- [core-contributor.md](core-contributor.md) — `CORE-01` … `CORE-06`
- [extension-author.md](extension-author.md) — `EXT-01` … `EXT-06`
- [site-developer.md](site-developer.md) — `SITE-01` … `SITE-06`
- [cross-cutting.md](cross-cutting.md) — `META-01` … `META-04`

## Environments

A scenario is only meaningful in the working directory it names, because
instance discovery starts where the MCP client launched the server. Start the
client in that directory, then paste the prompt.

| Id | What it is |
| --- | --- |
| `E-CORE` | A TYPO3 core checkout (`typo3/typo3`), dependencies installed. |
| `E-SITE` | A Composer site installation, ideally under DDEV and on a version whose console has `language:domain:search`. |
| `E-EXT` | A standalone extension repository with its dependencies installed, TYPO3 among them. |
| `E-NONE` | A directory with no TYPO3 installation anywhere above it. |
| `E-STOPPED` | `E-SITE` with the DDEV project stopped. |

## Status of a scenario

| Mark | Meaning | What a run is for |
| --- | --- | --- |
| `covered` | The server should answer this well today. | A bad answer is a regression; fix it. |
| `boundary` | Deliberately outside scope. | The right answer is a clean decline plus where to go instead. A confident invented answer is the failure. |
| `partial` | Answered, but not for every audience or not to the depth the task needs. | Record which half was missing. |
| `gap` | An accepted requirement that is not met yet, or knowledge that does not exist. | Expected to fall short. Record what the task actually needed, not that it fell short. |

`boundary` and `gap` are not the same thing. A boundary is an answer — "this
server does not cover it, the documentation does" — and it can be given
perfectly. A gap is an answer that ought to exist and does not.

## Running one

1. Start the MCP client in the environment the scenario names.
2. Paste the prompt verbatim. Add nothing: no tool names, no hints that a
   TYPO3 knowledge server is attached, no correction when the agent goes the
   wrong way. What the agent does with an under-specified request is part of
   what is being measured.
3. Let the session run to the end of the task, not to the first tool call.
   Whether a checklist is usable shows up when it is worked off, not when it
   is printed.
4. Grade against **What has to come out of it** and **How it fails**.

## What a run produces

A run that went well produces nothing but a note in your head. A run that did
not produces one of two things:

- `typo3_feedback_record` for what was missing, wrong, or unhelpful — with the
  scenario id in the observation, so the note can be traced back to the task
  that exposed it. This is the normal outcome for `gap` and `partial`.
- A new scenario, when the session went somewhere this suite does not describe.
  That is the more valuable outcome of the two.

From there the usual route applies: the note is worked off in a commit that
deletes it, and what has to keep holding afterwards goes into
[requirements.md](../requirements.md). See [AGENTS.md](../AGENTS.md).

For a `gap` scenario, do not re-file the part that is already written down —
its **Status today** line names the requirement. File what the task needed
beyond it.

## Coverage

Rows are task shapes, columns are audiences. A cell names the scenarios that
cover it; an empty cell is a hole in this suite, not a hole in the server.

| Task | Core contributor | Extension author | Site developer |
| --- | --- | --- | --- |
| Bug fix | `CORE-01` | `EXT-03` | `SITE-03` |
| New feature / new code | `CORE-02` | `EXT-04` | `SITE-05` |
| New project from scratch | — | `EXT-02` | `SITE-01` |
| Upgrade to a new TYPO3 version | `CORE-04` | `EXT-01` | `SITE-02` |
| Testing | `CORE-05` | `EXT-05` | `SITE-06` |
| Review, commit, submission | `CORE-03` | `EXT-03` | — |
| Labels, icons, i18n | `CORE-02` | `EXT-06` | `EXT-06` |
| Frontend / theming | — | — | `SITE-04` |
| Version and branch spread | `CORE-06` | `EXT-01` | `SITE-02` |
| Orientation and degraded setup | `META-01` … `META-04` | | |
