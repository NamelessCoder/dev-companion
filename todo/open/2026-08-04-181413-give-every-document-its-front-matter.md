# Give every document its front matter

**Serves:** knowledge/documents/, src/Knowledge/
**Priority:** normal

`D-KNW-057` is what this carries out, after the move card. Give each document a
`---` front matter with `description`, `whenToUse` and `hints`, parsed with
`symfony/yaml` because a description carries colons; strip it in
`Documents::sections()` so it is neither searched nor handed over, the way the
version binding is stripped. `Documents::description()` composes the resource
card from `description` and `whenToUse` plus the sentence its scope decides,
instead of from the `covers` row's `topic`. Have `typo3_hint_lookup` name every
document whose `hints` list carries the id it is answering with, which is the
crossing `references.json` already models with its own `hint` field. Hold each
listed id to an existing hint, and put the assertions on `D-KNW-057`'s **Covered
by**.
