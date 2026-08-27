---
date: 2026-08-25T11:06:59+00:00
category: idea
status: closed
closed: 2026-08-27
model: claude-opus-5[1m]
tool: typo3_gerrit_lookup
directory: /home/benji/projects/typo3-cms
---

# A cherry-pick that landed with conflict markers is only visible if messages is raised off its def...

## Observation

Task: review change 95392 against the checkout, then rebase its 14.3 backport 95412.

typo3_gerrit_lookup defaults messages to "none", documented as a size decision: "it is 57.9 KB against 14.3 KB on a change with 21 patch sets". That default hid the single most important fact about change 95412.

95412 was created with Gerrit's web "Cherry pick" action and landed with unresolved conflict markers committed into a shipped JavaScript file. Gerrit records that as a change message and nowhere else:

  {"author":"Benjamin Kott","patchSet":1,"bot":false,
   "message":"Patch Set 1: Cherry Picked from branch main.\n\nThe following files contain Git conflicts:\n* typo3/sysext/form/Resources/Public/JavaScript/backend/form-editor/view-model.js"}

Everything else the answer carried reads as a healthy new change: status NEW, patchSet 1, commentCount 0, comments [], both labels present with empty vote arrays, a clean subject, a resolved Forge issue with a matching subject. A reviewer trusting the default would conclude "fresh patch set, nobody has looked at it yet" and would be wrong in the way that matters - the change is not merely unreviewed, it is broken, and merging it kills the form editor on 14.3.

I only saw it because I passed messages="people" out of habit carried over from the previous call in the same session, not because anything told me to. That is luck, and the next session will not have it.

The asymmetry is worth naming: commentCount and comments are about humans reviewing the change; the conflict report is the server describing the change's own integrity, and it is filed in the same bucket as the CI pings that the size argument is aimed at. The default correctly suppresses 20 bot pipeline reports and incorrectly suppresses this.

Two smaller notes from the same two calls, both good and worth keeping: the "Outdated Votes: * Verified+1 (copy condition: changekind:NO_CHANGE)" line in a message is what explained why core-ci's Verified+1 was gone from patch set 3 after a commit-message-only edit - the labels array alone looked like a change nobody had verified. And the issues array, joining the change to Forge issue 110502 with its subject, tracker and status, was what let me confirm the change number named the patch I thought it did without a second call.

## Query

typo3_gerrit_lookup(change="95412", messages="people"). The same call with messages omitted (the default "none") would have returned commentCount 0, an empty comments array, empty label votes, and nothing at all about the change being broken.

## Suggestion

Lift the integrity facts out of messages into the top-level change object, so they survive the default:

- A field such as "conflicts": ["typo3/sysext/form/Resources/Public/JavaScript/backend/form-editor/view-model.js"], parsed from a "Cherry Picked from branch X ... The following files contain Git conflicts:" message, present whenever such a message exists on the current patch set. Empty or absent otherwise.
- A field saying the current patch set was created by a web cherry-pick rather than a push, since that is what makes the conflict case likely in the first place.
- Optionally the same treatment for the "Outdated Votes" line, as a per-label note saying a vote was dropped and why, so the labels array explains itself.

Failing that, at minimum: make the messages parameter description say that a web cherry-pick's conflict report lives there and is invisible under the default, and say it in the tool description too - the person who needs the warning is the one who never passes the parameter and therefore never reads its description.

A cheaper variant that keeps the size budget: keep messages="none" as the default but always include the latest non-bot message when it matches the conflict pattern.
