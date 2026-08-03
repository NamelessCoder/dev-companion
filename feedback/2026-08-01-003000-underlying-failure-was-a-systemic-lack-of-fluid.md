---
date: 2026-08-01T00:30:00+00:00
category: missing-knowledge
status: open
model: opencode/deepseek-v4-flash-free
tool: typo3-content-element-development, typo3_documentation_lookup
directory: /home/benji/projects/site-new
---

# Debrief of the TYPO3 14 testimonials session: the underlying failure was a systemic lack of Fluid...

## Observation

Debrief of the TYPO3 14 testimonials session: the underlying failure was a systemic lack of Fluid understanding, not isolated viewhelper mistakes. The assistant used <f:if> without knowing that <f:else> forces an explicit <f:then>, used <f:link.typolink> inside a conditional branch without knowing its output is swallowed when the branch structure is wrong, guessed that a relational select field is iterable with f:for, and could not determine how a RecordInterface field ({record.field}) resolves in a preview template — it tried to reverse-engineer StandardVariableProvider/getByPath instead of knowing the Fluid object-access model. The external-URL company link rendered empty and the backend preview showed nothing until the user intervened. The gap is not one viewhelper signature; it is the Fluid parsing/rendering model (conditionals, object access, collections) as a whole, which this session demonstrated the assistant lacks and the knowledge base never supplied.

## Query

Fluid f:if/f:then/f:else structure, object access on RecordInterface, iterating relation fields, typolink inside conditionals

## Suggestion

Provide a Fluid reference at the level this task needed: the conditional ViewHelper contract (f:then required when f:else present, and that only the matched branch renders), how object-access resolves fields on a Record object, and whether/when a relation field is iterable with f:for. This should be reachable through the task guides before a viewhelper is written.
