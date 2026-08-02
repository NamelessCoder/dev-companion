# A form set is a registration the extension answer never reaches

**Serves:** feedback/2026-07-31-193109-task-typo3-extension-conformance-audit-what-i.md
**Priority:** low

Step 1b, on `D-ANS-015`: `Extension::ROOT_FILES` is a fixed list of paths, so
`Configuration/Form/<SetName>/config.yaml` is named by no part of
`typo3_extension_scope` — neither read nor said to be unread, which is what
`R-ANS-012` asks of a file the answer does not open. Read
`FormYamlCollectorConfigurator` and `FormYamlCollector` in `.checkouts/14.3` for
what makes a directory a form set and what identifies one — `name:` in the file
rather than the directory — and confirm against `.checkouts/13.4`, which has
neither class, that the answer is bound from v14.2 (#109412). Then decide whether
this is a section of its own beside "Site sets" or a directory convention the
registration-file list learns, and what a set with an unreadable `config.yaml`
answers.
