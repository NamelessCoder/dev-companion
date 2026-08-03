# `typo3_feedback_record`

Leave feedback about a gap, wrong answer, or missing capability of this
knowledge server — and about what it did well, because what worked is what must
not be broken later. Stored as markdown in this server's own checkout and read
back with typo3_feedback_list, not in the project you are working in, so do not
look for the file there. One feedback per subject: a feedback carrying three
complaints is worked off three times over or not at all.

`readOnlyHint: false` · `destructiveHint: false` · `idempotentHint: false` · `openWorldHint: false`

## Takes

```yaml
# What was missing, wrong, or unhelpful, specific enough to act on later, in
# English. Open with one line naming the task you were given, so the feedback
# can be traced back to what exposed it. A finding is the path a value was read
# at, the shape of what came back, and where it came from — never the value
# itself where the installation keeps it secret: an encryption key, a password,
# a token, the credentials in a connection string. This is committed and pushed
# into a checkout that installation's owner is not watching, so a secret pasted
# here as proof has left it for good. The finding is "the key at
# SYS/encryptionKey is the active one, hardcoded in config/system/settings.php";
# the 96 characters after it establish nothing further.
observation: string
# The model recording this feedback, as it identifies itself, for example
# claude-opus-5 or gpt-5.3-codex. Read it where it is written down — what your
# client reports for the current session, or the person running you — rather
# than from what you remember about yourself: a feedback is evidence about one
# model's behaviour, and one filed as "unknown" cannot be told apart from
# another model's. That fallback is for a session that looked and could not find
# out; an invented identifier is worse than none.
model: string
# One of: missing-knowledge, wrong-answer, tool-gap, bug, idea.
# missing-knowledge: the knowledge base lacks the answer. wrong-answer: the
# answer was incorrect. tool-gap: no tool covers the need. bug: the server
# misbehaved. idea: anything else.
category: string  # optional
# The tool the observation is about, for example typo3_component_lookup, or the
# skill it activated, for example typo3-extension-conformance. Several are named
# in one string, separated by commas.
tool: string  # optional
# The arguments that produced the unsatisfying result, or the task text where a
# whole session is what produced it, so somebody can re-run the feedback against
# a later version of the server instead of reading it. The rule from observation
# holds: the arguments and the path they named, never a value the installation
# keeps secret. A re-run needs to know that SYS/encryptionKey was asked for and
# that a key came back, not what the key was; a password or a token that was
# itself an argument is named rather than quoted.
query: string  # optional
# What the server should have answered or should be able to do instead.
suggestion: string  # optional
```

## Answers with

```yaml
# Path of the recorded feedback, relative to this server's own checkout.
file: string
# The same feedback as an absolute path. It is in the server's checkout, not in
# the project the feedback was recorded from.
path: string
# What was removed before the feedback was written, one entry per value, naming
# the field it stood in and the shape it had. Empty where nothing was removed,
# which is the ordinary case. Each removal stands in the file as a [redacted:
# ...] marker, so the report says of itself that it was altered.
redacted: [string]
# What was cut for length before the feedback was written, one entry per field,
# naming the field and how much of it went. Empty where nothing was cut, which
# is the ordinary case. Each cut stands in the file as a [cut: ...] marker where
# the field stops, so the report says of itself that it is short of what was
# written.
cut: [string]
```

## Not answered

And deliberately: it is the one tool here that writes, and this table has two
drivers rather than one. A call recorded from it would file a real feedback into
the open ones every time `ToolContractTest` runs, not only when the recording
does.
