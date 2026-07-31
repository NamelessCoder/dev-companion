---
id: D-ANS-3
date: 2026-07-30
status: standing
---

# D-ANS-3 — Retrieval stays lexical and runtime inspection stays narrow

**No embedding dependency, no semantic index, and no generic SQL, log or
database-schema tool: what version, audience, binding and source decide is not a
gap a semantic match would close.**

The live-documentation source created the point at which the remaining search
and inspection gaps could be measured instead of anticipated.

- **Evidence:** `bin/cli hints coverage` finds every one of the 61 hints from its
  own title. Seven of 25 scenario prompts reach no architecture hint:
  `CORE-06`, `META-01`, `META-04`, `META-05`, `EXT-01`, `EXT-05`, `SITE-03`.
  They are respectively version spread, orientation, structured-only output,
  installation, an upgrade, the explicit testing boundary and effective runtime
  configuration. Their owning route is not an architecture hint, so a semantic
  match would make the report look fuller without answering them better.
- **Decided:** no embedding dependency or semantic index is added. The concrete
  live-docs ranking defect was lexical — separated words tied a precise
  multi-word title — and is fixed by weighting adjacent query terms, guarded by
  `DocumentationTest`. Semantic retrieval may nominate candidates in future,
  but version, audience, binding and source still decide what can be returned.
- **Decided:** no generic SQL, log or database-schema tool is added. No feedback
  note or new scenario needed one after live documentation and the existing
  installation diagnostics were available. A runtime tool starts with the
  session that could not finish without it, not with parity against another
  server.
- **Wrong if:** real queries repeatedly miss a present section after short
  English alternatives, or a scenario's diagnosis cannot be completed from
  project files, effective configuration and the caller's own checkout. Record
  that session; it supplies both the tool boundary and the safe result shape.
