# Record what the installation-backed tools answer when a console answers

**Serves:** documentation/clients/tool-answers/
**Priority:** normal

Point `bin/cli tools:record` at an installation whose console answers, and
settle first what then becomes of the shapes the current pages are made of. The
recording ran against `.checkouts/14.3`, which has no console, so four answers
are an `unsupported` object and nothing else — `typo3_schema_lookup` on both its
calls, `typo3_configuration_lookup`, `typo3_backend_module_lookup` — and ten
more carry `answeredBy: "packages"` across `typo3_label_lookup`,
`typo3_icon_lookup`, `typo3_fluid_namespace_list`, `typo3_extension_scope`,
`typo3_project_scope` and `typo3_changelog_lookup`. The value `installation`,
which `tools.md` documents beside `packages`, is on no page at all, so the
answer a client gets from a booted TYPO3 is the one shape the recording never
shows. A page holds one recording and one head, so recording against a
console-answering installation trades the two shapes above for that one:
decide whether a page gains a section per installation, whether two heads are
worth the machinery in `ToolAnswers`, or whether the unsupported shape is
better shown once in `tools.md` than in every page. `D-DOC-006` counted all
three shapes in one run as evidence, so the answer goes onto its foot. Which
installation may be recorded against at all is what
`todo/progress/2026-08-02-120402-make-the-environments-a-run-validates-in-creatable-here.md`
is settling — a recording pointed at `E-SITE` on one machine is repeatable by
nobody else, which is the reason `tools:record` defaults to a checkout today.
