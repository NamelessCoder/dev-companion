# Weigh the scope answer against what a caller asks it for

**Serves:** D-ANS-087
**Priority:** normal

`typo3_server_scope` answers 86,061 characters in one call — twelve times
`typo3_project_describe`, and the heaviest thing this server hands over.
`covers` is 22,813 of it and `doesNotCover` 7851, measured on 2026-08-19 and
recorded in `D-ANS-087`, which decided the project answer stays whole and moved
the weight question here.

What this todo has to settle, in this order:

- **Whether the weight is a problem at all.** `D-FBK-020` prices calls rather
  than tokens, so the same argument that keeps the project answer whole applies
  here until something shows otherwise. The evidence that would settle it is a
  filed session that called this tool and lost something to its size, or a client
  that truncates it. Neither exists today, and if neither can be found the answer
  is a paragraph in `D-ANS-087` saying so.
- **What a topic is, if it is one.** An orientation answer has parts a caller can
  name — what is covered, what is not, which installation is read, how a checkout
  is discovered. A caller that knows it wants the boundary is not choosing blind,
  which is what made a selection wrong for the project answer.
- **What may not become selectable.** `R-SCO-009` names what a caller cannot take
  away, and the `instructions` are what a deferring client gets whole
  (`D-AUD-011`). A selection that can hide the boundary is the version to reject.

Measure before and after with `bin/cli tools:measure`, which reads the recorded
answers rather than calling anything.
