# Say in `typo3_feedback_record` that a secret is not evidence

**Serves:** R-FBK-011
**Priority:** normal
**Branch:** todo/say-in-typo3-feedback-record-that-a-secret-is-not
**Claimed:** 2026-08-02

Extend the `observation` and `query` field descriptions in
`src/Tool/FeedbackRecord.php` so a session recording feedback is told what a
finding needs — the path, the shape and where the value came from — and that a
value the installation keeps secret is not part of it, naming a key, a password
and a token so the rule is recognisable rather than abstract. Both fields,
because the feedback this came from put the key in each of them. Then run
`bin/cli tools:index` to bring `documentation/clients/tools.md` along, and set
`R-FBK-011` to what the wording alone can hold — `not guarded` stays honest
until something reads the corpus. Whether `Channel::record()` should also refuse
or redact is the next question and not this step:
[`D-FBK-019`](../../decisions/feedback/fbk-019-a-recorded-feedback-is-stored-as-it-was-written-secrets-included.md)
says why it was left open, and its **Wrong if** is what would settle it.
