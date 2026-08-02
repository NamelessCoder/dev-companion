# Let the channel redact what a session should not have pasted

**Serves:** R-FBK-011
**Priority:** high

Make `Channel::record()` remove a value that looks like a credential from
`observation` and `query` before either is written, replacing it with a visible
marker and saying in the answer that it did — both fields, because the feedback
this came from put the key in each. The threshold is what makes it usable: a
hexadecimal run of 64 characters or more catches the 96-character
`SYS/encryptionKey` this started with and walks past every git SHA, which is 7
to 40 and quoted constantly in feedback about core patches. What else counts —
long base64, a value next to `password`, `token`, `secret` or a DSN with
credentials — is the part to settle while writing it, against the archive as the
corpus to test against, because a rule that redacts a version string costs more
than the leak it prevents.

Marked rather than silent. The archive keeps a session's report because it is
evidence, so a reader has to be able to see that something was taken out and
ask; a quiet substitution is a second thing that alters a report, which is
exactly what `D-FBK-019` refused when it left the recorded file alone.

This closes the question that entry left open, and it is being closed before its
**Wrong if** fires rather than after. The reason is in that entry's own
**Assumed**: it doubts that a field description reaches a session at call time
and names `D-AUD-003` as recording the opposite. The session that pasted the key
was proving the tool returns the live runtime value, so a sentence asking it not
to would have stood against what it was doing at that moment. Waiting for the
second leak means waiting for a second credential in a pushed repository, and
that is not a symmetric trade with a feedback that reads a little vaguer. Give
`D-FBK-019` a **Since then** saying so with the commit, and restate `R-FBK-011`
on what then holds it — the guard rather than the telling — leaving it
`not guarded` only for whatever the wording alone still carries.
