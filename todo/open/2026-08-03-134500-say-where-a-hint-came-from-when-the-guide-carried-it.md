# Say where a hint came from when the guide carried it

**Serves:** scenarios/
**Priority:** normal

The fifth recorded `REVIEW-03` run quoted two hints as `typo3_hint_lookup` and
never called it. The rules are correct and came from this server:
`typo3_task_guide` returned them with their domain names, `fluid-viewhelpers`
and `core-tests` among them. So a report following the checklist's demand that a
finding name the lookup that owns its rule sends its reader to a tool that did
not answer.

Two candidates and neither is obvious. The guide's own answer could say which
hints it carries and that they came from `typo3_hint_lookup`, which puts the
correction where the payload is and costs nothing at the call site. Or the
review skill could stop routing to `typo3_hint_lookup` separately, since three
runs called it, two did not, and nothing in the two answers reads poorer for it
— but that is a claim about every review rather than about these five, and
`D-SKL-009`'s closing section is the only reading behind it.

Establish which before changing either: read what `typo3_task_guide` puts in its
payload today against what `typo3_hint_lookup` answers for the same paths, and
say whether the guide's copy is the whole of it or a selection.
