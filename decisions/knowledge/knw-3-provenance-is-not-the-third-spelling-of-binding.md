---
id: D-KNW-3
date: 2026-07-30
status: tested
---

# D-KNW-3 — `provenance` is not the third spelling of `binding`, and stays

**The task intents move to `binding` and `provenance` stays as it is, because
its `installation` value is not an obligation at all.**

The plan was one field name for who an answer obliges, because it looked spelled
three ways: `binding: "core"` on the hints, `coreOnly: true` on the task
intents, `provenance: "core-only"` in `knowledge/server-scope.json`. Two of them
are the same axis. The third is not, and reading its values is what showed it.

- **Decided:** the intents move to `binding`, because `coreOnly: true` asks
  exactly the question `binding` asks and answers it in a boolean — which is the
  shape that cannot carry a second audience the day one is needed.
- **Decided:** `provenance` stays as it is. Its values are `core-only`,
  `transferable` and `installation`, and the third is not an obligation at all —
  it says the answer is read from the installation rather than from a snapshot.
  Folding it into `binding` would either drop that value or make "installation"
  something a caller is obliged by. Two fields overlapping on one value are not
  one field with a naming problem.
- **Wrong if:** a fourth value arrives on either side that reads naturally in
  both — then they are the same axis after all and the merge is the entry that
  was right.
- **Tested on 2026-08-02:** no fourth value has arrived on either side. Read out
  of the corpus as it stands, `binding` is one value — `core`, on 28 entries
  across the six `knowledge/architecture-hints/*.json` files and
  `knowledge/task-intents.json`, hint-level and statement-level alike — and
  `provenance` is the recorded three, on the 16 covered topics in
  `knowledge/server-scope.json`: four `core-only`, four `installation`, eight
  `transferable`. The move this entry decided is done: the intents carry
  `binding` and no `coreOnly` boolean is left in either corpus.
- **Since then:** the arrival of a fourth value was already caught, in two
  places that do not know about each other —
  `VersionsTest::whoIsObligedIsWrittenAsDataToo` holds `binding` to its one
  value across both corpora, `ScopeTest::everyCoveredTopicSaysWhatItIsWorthOutsideTheCore`
  holds `provenance` to its three. Neither holds the pair, which is what this
  entry turns on: a session widening one vocabulary edits one pin and is never
  asked whether the new value reads on the other axis.
  `KnowledgeTest::whoAnAnswerObligesAndWhatItIsWorthStayTwoVocabularies` is that
  assertion — both sets read from the corpus, pinned, and asserted disjoint,
  with this id in the failure message. It compares spellings: `core` and
  `core-only` are the overlap this entry already looked at and kept, so a
  normalised comparison would have failed on the day it was written, and the
  pinned sets are what catch a fourth value arriving under a spelling the
  intersection would miss.
