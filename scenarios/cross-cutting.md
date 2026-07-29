# Cross-cutting

These are not about one audience. They are about the four situations every
audience runs into: not knowing what the server is for, working somewhere it
cannot reach an installation, working on a task whose audience is genuinely
ambiguous, and using a client that shows the agent only the data half of an
answer.

See [readme.md](readme.md) for how to run one and what the marks mean.

---

## META-01 — What can you actually help me with

**Environment:** `E-SITE` · **Status today:** `covered`

> Before I let you loose on this project: what do you know about TYPO3, and where
> do you stop? Be specific, I have been burned by confident nonsense before.

**What the agent needs from this server**

- What is covered, at which depth, and by which tool.
- What is deliberately not covered, and what to use instead.
- Which installation, if any, is being read, and whether its console is
  reachable.

**What has to come out of it**

- The answer separates three things that are usually mixed: what comes from the
  bundled knowledge, what is asked of this installation, and what the agent has
  to establish in the checkout itself.
- The installation being read is named, along with how it was found.
- The boundaries are stated as boundaries, not as "I can probably help with
  that too".

**How it fails**

- A capability list that describes TYPO3 rather than this server.
- Silence about which installation is being read, so the user cannot tell
  whether later answers were about their project at all.

---

## META-02 — Nothing to ask

**Environment:** `E-NONE`, then `E-STOPPED` · **Status today:** `covered`
— `R-DIS-6` … `R-DIS-9`, `R-ANS-1`, `R-ANS-5`

> Which icons can I use for a "publish" action, and is there already a label for
> "Publish page"?

Run it twice: once in a directory with no TYPO3 anywhere above it, and once in a
site project whose DDEV is stopped. The prompt stays the same; only the
environment changes.

**What has to come out of it**

- Neither run answers "there are no such icons" or "there is no such label".
  Both say that the installation could not be asked, and why.
- The two reasons are distinguishable: nothing found versus found but not
  running.
- Where discovery failed, the answer names where it looked.
- The stopped project is reported with the command that would start it, and
  nothing is started as a side effect.
- The way out is named: the two environment variables that end the guessing.
- After the user starts DDEV and asks again in the same session, the answer
  changes — a negative is not remembered (`R-DIS-9`).

**How it fails**

- An empty result shaped like a complete one, and the agent concluding the
  extension registers nothing.
- DDEV started by the agent because a lookup wanted an answer.
- The session having to be restarted after the installation became reachable.

---

## META-03 — Two audiences in one directory

**Environment:** `E-SITE` with the extension under `packages/` ·
**Status today:** `gap` — `R-AUD-2`, `R-SCO-1`

> I am touching `packages/acme_events/Classes/Domain/Repository/EventRepository.php`
> and `typo3/sysext/core/Classes/Database/Query/QueryBuilder.php` in the same
> session — the second one because I think the bug is actually in the core.
> Tell me what applies to each.

**What the agent needs from this server**

- To keep the two apart: one path is core work with everything that entails, the
  other is extension work with none of it.
- To say which is which from the shape of the paths, without being told in prose.

**What has to come out of it**

- The core path gets the core conventions, the core checks and the submission
  route; the extension path gets the conventions that transfer and nothing else.
- Where the audience genuinely cannot be decided, the answer says it is uncertain
  rather than picking one silently (`R-AUD-2`).

**How it fails**

- One verdict for the whole session, applied to both paths.
- The distinction only appearing after the user spells out "this is not core"
  (`R-SCO-1`).

---

## META-04 — The client shows only the data

**Environment:** `E-SITE`, with a client that renders `structuredContent` and
drops the text block · **Status today:** `covered` — `R-ANS-2`

> Same task as `SITE-03`, run in that client.

**What has to come out of it**

- Everything the agent needs in order to act is in the structured data: whether a
  lookup was answered by the installation or by nothing, the reason where it was
  not, the installation and console diagnostic, the coverage and source of a
  match.
- The session reaches the same conclusion it reaches with the text visible.

**How it fails**

- A conclusion that only holds because a sentence in the text block was read —
  and therefore silently inverts in this client (`R-ANS-2`).
