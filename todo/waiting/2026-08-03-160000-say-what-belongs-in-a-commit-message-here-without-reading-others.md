# Say what belongs in a commit message here, without reading others

**Serves:** decisions/
**Priority:** normal
**Waiting on:** does this repository hold itself to what its own
    `typo3_commit_message_guide` returns for `workflow="project"` — the keyword
    set, the 52-character subject, the 72-character body — or does it keep the
    79 its prose measure wraps at and the longer subjects its history is
    written in? The two cannot both be right, and whichever wins has to be
    written out rather than inferred. Second half of the same answer: is it a
    `commit-msg` hook beside the `pre-commit` that already runs, or a sentence
    somebody rereads?

Write the answer into the `## Commits` section of
[`AGENTS.md`](../../AGENTS.md), which today states the shape of a body and names
not one keyword — so the set is learned by reading `git log`, which is the thing
this card exists to end. What the history uses, over 819 commits: 567 `[TASK]`,
82 `[FEATURE]`, 26 `[BUGFIX]`, 5 `[DOCS]`, no `[!!!]` at all, and 139 with no
keyword whatever. The standard is already inside this repository and describes
this case in its own words — `workflow="project"` is "any other repository —
the keyword, the 52/72 character limits and the wrapping are checked, no
trailer is added or demanded" — over the enum `BUGFIX`, `FEATURE`, `TASK`,
`DOCS`, `SECURITY`. Adopting it verbatim contradicts two things that are true
today: 611 of the 819 subjects run past 52 characters, at a median of 61 and a
maximum of 97, and the body wrap disagrees with itself, because the tool wraps
at 72 while the bodies here sit at the prose measure's 79 at the ninetieth
percentile. The sentence the answer replaces is in that same section and says
"Nothing measures a commit message", which a `commit-msg` hook would make false.
