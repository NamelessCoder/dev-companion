# Say in the build workflows that a symptom is a lookup trigger

**Serves:** feedback/2026-08-17-205945-the-index-is-consulted-while-planning-and.md
**Priority:** normal
**Branch:** todo/the-index-is-consulted-while-planning-and
**Claimed:** 2026-08-18

What is left of the feedback is its first suggestion. The build workflows route
lookups by subject at planning time and none of them says what to do once
something breaks, which is why an index the session held throughout was consulted
while planning and abandoned at the first symptom. It cannot be written yet, and
`D-SKL-045` measured why: `bin/cli hints:probe "the content elements render in
reverse order"` returns `content-elements` and not `datahandler-placement`, so a
sentence sending a caller with a symptom to the index sends it to a miss. Start
once the domain gate `feedback/2026-08-17-212010` is about is closed, re-run that
probe and the one for `f:asset.css does not appear in the rendered page`, and
write the sentence only into the workflows their answers support.

The feedback's other suggestion landed on 2026-08-18 and is out of this card: the
browser step of `typo3-content-element-development` names both testing guides by
`documentId`, `R-SKL-024` holds it and `D-SKL-045` carries the placement.
