---
date: 2026-08-24T12:24:34+00:00
category: idea
status: closed
closed: 2026-08-24
model: claude-opus-5[1m]
tool: typo3_documentation_lookup
directory: /home/benji/projects/typo3-cms
---

# I dropped a sentence from a changelog rather than ask whether the behaviour is documented

## Observation

Task: review Gerrit change 95179 and work off its review comments, which meant writing an Important changelog entry for a behaviour change in the stdWrap property "override".

While drafting I wrote the sentence "...and applies every other value, 0 included, as documented." Then I deleted "as documented", because I did not know whether the TypoScript Reference on docs.typo3.org actually states what `override` does with a falsy value, and a changelog that claims the fix restores documented behaviour is wrong if the documentation never said it. So the published entry is weaker than it could have been: it describes the new behaviour without being able to say whether it matches or contradicts the reference.

typo3_documentation_lookup was in my deferred tool list and I never loaded its schema, let alone called it. My assumption was that the TypoScript Reference lives in a separate repository from this core checkout and that a documentation lookup here would be about the Documentation/ folders inside sysexts. I did not test that assumption; I just did not expect an answer and moved on.

The same gap has a second edge. In reviewing the diff I found that stdWrap_ifEmpty at ContentObjectRenderer.php:1335 uses `empty(trim((string)$content))` and therefore has the exact same "0 counts as empty" behaviour as the bug being fixed. Whether that is a bug or the documented intent of a property literally named ifEmpty is a documentation question, not a code question. I could not answer it, so I reported it to the user as "another issue, not this patch" rather than as a finding. A documentation lookup that could quote what the reference says about ifEmpty vs ifBlank would have turned a hedge into a verdict.

## Query

Never called. The questions I would have asked: "TypoScript stdWrap override — what does the reference say happens when the value is 0 or empty?" and "TypoScript stdWrap ifEmpty vs ifBlank — is a content value of 0 documented as empty?"

## Suggestion

If typo3_documentation_lookup reaches docs.typo3.org and not only the Documentation/ folders in the checkout, say that in the first line of its description, naming the TypoScript Reference and the TCA Reference explicitly. An agent writing a changelog entry about a TypoScript property needs to know whether the reference already describes the behaviour it is changing, and that is the moment the tool is worth calling. If it does not reach the external manuals, saying so is just as useful — I would then have known to go to the web rather than silently weakening a sentence.
