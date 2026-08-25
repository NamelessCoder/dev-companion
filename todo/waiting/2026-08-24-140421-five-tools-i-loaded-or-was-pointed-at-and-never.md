# Five tools I loaded or was pointed at and never called, and the one schema I guessed wrong

**Serves:** feedback/2026-08-24-140421-five-tools-i-loaded-or-was-pointed-at-and-never.md
**Priority:** normal
**Waiting on:** whether `typo3_project_describe` volunteers the deprecated-files
    verdict for the extensions that are the project's own, so a session that
    skips step 2 of `skills/base.md` still gets it. That is
    `documentation/records/judging.rst` step 5 — everything was delivered and
    the design is the price — and it changes what an orientation answer hands
    every caller. The reading behind it is in
    [`D-GUI-012`](../../decisions/guides/gui-012-the-brief-names-the-guide-the-recognized-work-belongs-to.md),
    under the entry of 2026-08-25.

Judged on 2026-08-25, and the feedback is trimmed to the half above. The five
tools it names turned out to be four different things and only one of them was a
lever this repository could pull:
[`D-KNW-120`](../../decisions/knowledge/knw-120-a-hint-that-states-a-merge-names-the-lookup-that-reads-the-result.md)
carries all four verdicts and the change that closed the first.

What the reading established about the half that is left, so it is not done
again. Nothing was missing and nothing was misworded. `skills/base.md` step 2
already says *What it does not ship is answered too, and that is the half no
file listing can give you*, which is aimed at the session's own reason for
skipping — it had read every file by hand. `ExtensionDescribe::description()`
names the deprecated-files verdict in full, the four files and the predicate
each turns on, and under a deferring client that description arrives with the
schema the session says it loaded. So the surface a tool is chosen on was in
context, complete, and the call was not made.

The next step once it is answered: the field is `deprecatedFiles`, built by
[`D-ANS-009`](../../decisions/answers/ans-009-a-shipped-file-deprecation-is-found-by-the-tool-that-lists-the-file.md),
computed in `Extension::deprecatedFiles()` from the registration files that
answer already lists — which `typo3_project_describe` does not list. So the
shape is what to settle first: the verdict per own extension in the project
answer, or a sentence in it naming the call that carries it. That entry's second
**Wrong if** is what either has to be built against — an answer that volunteers
deprecations being read as a compatibility verdict.
