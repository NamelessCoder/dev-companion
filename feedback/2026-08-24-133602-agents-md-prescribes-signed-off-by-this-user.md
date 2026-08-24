---
date: 2026-08-24T13:36:02+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# AGENTS.md prescribes Signed-off-by, this user does not use it — a guide reading the repo hands ba...

## Observation

Task: after finishing a Core patch, draft the commit message for it against the TYPO3 Core conventions.

I wrote the message from the repository's AGENTS.md and my own project memory, and did not call typo3_commit_message_guide even though the project memory file loaded into this session names it first in a list headed "Ask it first". Two drafts went out with a Signed-off-by: trailer. The user struck it both times. Checking afterwards, my own memory file feedback_commit_style.md already said, in as many words, "Do not add a Signed-off-by: trailer. Even though AGENTS.md in the TYPO3 core checkout prescribes it, the user does not use it."

The part worth reporting is not that I forgot. It is the conflict underneath it. AGENTS.md, checked into this Core monorepo, says: "Sign off every commit — git commit -s appends the Signed-off-by: trailer, or set git config format.signOff true to get it automatically. It certifies that you wrote the patch ... (Developer Certificate of Origin)." It also gives the footer order as Resolves / Releases / Signed-off-by / Change-Id. A commit-message guide that derives its answer from the checkout would reproduce exactly the footer the user rejects, with a DCO justification attached that makes it hard to argue with.

Both positions are defensible — the DCO line is real project policy for patches pushed to review.typo3.org, and the person working here evidently does not use it. So this is not "the repo is wrong". It is that the guide's answer is underdetermined by the repo alone, and the deciding input is a per-person practice the server has no way to see.

Other things I got right without asking, so they are not at issue: subject tag [BUGFIX], imperative subject, prose body rather than bullets, no line reaching 72 characters, Change-Id left to the hook, Releases: derived from Typo3Version::BRANCH, and no RST changelog because a bugfix does not get one.

## Query

Never called, although the project's own MEMORY.md opens with "Ask it first: ... typo3_commit_message_guide". The message I drafted instead, twice, carried "Signed-off-by: <name> <email>" and the user struck it: "wir haben keine signed off messages". The footer that survived was: "Resolves: #110527" / "Releases: main, 14.3, 13.4", with Change-Id left to the hook.

## Suggestion

Where typo3_commit_message_guide answers on the footer, have it mark Signed-off-by as the one trailer that is contributor practice rather than a hook-enforced rule, and say so explicitly: the commit-msg hook validates Resolves/Releases and adds Change-Id, it does not require Signed-off-by, so a repository whose AGENTS.md prescribes it and a contributor who omits it are both consistent. A caller reading that would ask instead of emitting it, which is what should have happened here.

More generally: when the answer is read out of the working checkout's AGENTS.md rather than out of the Core's own convention set, saying which of the two it came from would let a caller weigh it. "AGENTS.md in this checkout says X; the commit-msg hook enforces Y" is actionable in a way that an undifferentiated "do X" is not.
