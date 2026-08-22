---
id: D-DIS-009
date: 2026-08-02
status: confirmed
---

# D-DIS-009 — Installed is one step short of callable, and the install is what says so

**`install` reports success on writing `.mcp.json`, which is one step short of a
callable tool, and it says nothing about the step that is left.**

Two sessions in the same project, three days apart, activated a published skill
with a correct entry beside it and found no tool they could call.

## Evidence

- Nothing was missing on this server's side. Re-run on 2026-08-02, driving
  `bin/typo3-dev-companion` over stdio from this checkout: `tools/list` returned
  24 tools, every one of the five the feedback names among them —
  `typo3_project_describe`, `typo3_extension_describe`, `typo3_task_guide`,
  `typo3_hint_lookup`, `typo3_changelog_lookup`.
- The entry in the reported project is correct and was written by this
  installer. `/home/benji/projects/site-new/.mcp.json` names `php` with the
  absolute path to the binary above, and `typo3-dev-companion.json` records
  `claude`, `generic` and `opencode` as the clients set up there.
- Reported twice, from that same directory, and only one of the two was ever
  read as a report about this server.
  `feedback/archive/2026-07-29-105130-overall-verdict-from-using-this-server-as-the.md`
  says the tools "were not registered in the Claude Code session despite a valid
  .mcp.json in the working directory, so every call in this evaluation went
  through a hand-written stdio JSON-RPC wrapper". It filed that as a process
  note, was archived on the strength of its other half, and nobody judged it.
  `feedback/archive/2026-07-31-185900-during-an-audit-of-the-printworks-3d-site.md`
  is the same failure on 2026-07-31, found the binary the same way, and found it
  only after the audit had been written. It was archived by the commit that
  answered it.
- What the command says when it finishes. Run into an empty directory on
  2026-08-02 it prints nine lines — one for `.mcp.json`, seven for the published
  skills, one for the `.gitignore` block — and every one of them is a past-tense
  success. Nothing in the output distinguishes the file being on disk from the
  client having read it.
- This repository already knows the failure shape, for one client and one
  artefact. `documentation/usage/installing.rst` carries "VS Code reads the
  skills only once it is told to": `chat.useAgentSkills` is off by default, so
  "nothing reports that six skills are sitting in the repository unread; a
  session there answers from the checkout as if none had been installed"
  (measured on VS Code 1.131.0, 2026-07-31). That is this finding one artefact
  over. There is no equivalent sentence for the `.mcp.json` entry, which every
  client install writes.
- Where it is written down is not where it is needed. The VS Code note is in a
  manual the person running `install` has no reason to open, and the run of
  2026-07-31 is what that costs.

## Decided

- The gap is step 2 of the ladder, delivery. The answer is here and it never
  reached the session, and the piece of this server that owns the delivery is
  the install rather than a skill or a hint.
- Queued rather than closed on the spot. The change is a line of installer
  output, which is `src/`, and what is left to do differs per client — a judging
  run has established the gap and not the fix.
  [`R-DIS-023`](../../requirements/discovery/dis-023-an-install-says-what-is-left-before-a-tool-can-be-called.md)
  says what must hold and a todo carries the next step.
- Against making the server reach further at runtime. Whether a client registers
  a project-scoped server is the client's to decide, and the feedback's own
  suggestion — expose the tools in the agent environment — names something no
  process here can do from inside a session that never started it.
- The precondition in `skills/base.md` stands unchanged, and the base does not
  learn the workaround. That is the question
  [`D-SKL-001`](../task-skills/skl-001-the-order-a-task-starts-in-is-one-file.md)
  handed forward, and it is answered in that entry rather than here.

## Assumed

- That the two reports have one cause. Both name a valid `.mcp.json` and a
  session with no tool in it, and both are from `site-new` with `claude`
  recorded as an installed client. Neither session could see the client's side,
  so what the client did with the file is inferred from what it did not do.
- That the person running `install` is the one who can finish it. Approving a
  project-scoped server, or restarting a session that was already open when the
  file was written, is theirs; nothing establishes that they read the output
  rather than scrolling past nine success lines.
- That naming the step is worth the line. `D-AUD-003` records a message at
  initialize failing to reach its reader, and this is a different position — a
  terminal, a person, at the moment they asked for something.

## Wrong if

- A third session reports the same thing after the install says what is left.
  Then the sentence is not the lever, and what is missing is the command
  checking rather than telling — asking the client, or refusing to call the
  install complete until something has connected.
- What is left turns out to be nothing on the clients that matter, and the two
  sessions were a stale process or a file written mid-session. Then this is one
  sentence about restarting, not a per-client property, and `R-DIS-023` is
  larger than the gap.
- The line lands and installs get read as failed — somebody sees a step
  remaining and concludes the command did not work. Then it cost more clarity
  than it bought.
- A session reaches a skill with the entry present, no tool callable, and drives
  the binary by hand anyway rather than stopping. Then the gap was never in the
  install: the precondition is not being followed, and that is `D-SKL-001`.

## Confirmed on 2026-08-02

Of the four **Wrong if** branches, the second was the one that could be settled
without another session, and it did not fire. Both mechanisms this entry
inferred are documented behaviour of the client `site-new` records, read the
same day in Claude Code's own manual: "Claude Code reads `.mcp.json` at session
start. Exit and restart the session after editing the file", and "the first time
Claude Code sees a project-scoped server, it asks you to approve it." So what is
left is not nothing on the client that matters, and the two sessions were not a
stale process — the **Assumed** bullet that inferred the client's side from what
it did not do is now read from the client instead, for that client.

Nor is it one sentence about restarting. Reading all eleven clients that get an
entry established three different answers: Amp and VS Code gate a workspace
server behind an approval or a trust confirmation of their own, Kiro and Droid
reload a saved file and need nothing at all, and five — Cursor, opencode, Zed,
Grok, and the Codex CLI beyond its trusted-project gate — do not answer the
question in their documentation, which is recorded as unestablished rather than
guessed. Per client is what the requirement had to be.

The other three branches still need a session that nobody has had yet: whether a
third report arrives after the line lands, and whether the line gets read as a
failed install, are both things only a reader can show.
[`R-DIS-023`](../../requirements/discovery/dis-023-an-install-says-what-is-left-before-a-tool-can-be-called.md)
is held from here on, and `documentation/usage/installing.rst` carries the
per-client sources.
