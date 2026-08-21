# Name the whole-file predicates beyond ext_tables.php and ext_emconf.php

**Serves:** feedback/2026-08-19-094315-what-kept-me-from-four-wrong-findings.md
**Priority:** normal

Judged step 4 and step 1a: the report's three hint items were wording and are
closed, and this half is the one thing no answer covers — `D-FBK-018` has the
reading. `Extension::deprecatedFiles()` carries two files, and its own docblock
states what a third has to be: a deprecation whose predicate is the file being
there rather than what the extension calls, which is why no changelog sweep
reaches it.

Two sessions have asked for the extension and neither named an instance, so the
judgement established that the set is bigger than two and stopped there.
`.checkouts/main` carries
`Documentation/Changelog/12.4/Deprecation-98093-Ext_iconAsExtensionIconFileLocation.rst`
— `ext_icon.png`, `ext_icon.svg` and `ext_icon.gif` at the extension root, a
deprecation log entry that becomes a silent stop.

First step: sweep the covered majors' changelogs for the rest, verify each
against `.checkouts/` on both sides of its boundary, and write the predicate and
the cost the way the two existing entries word theirs. It changes
`src/Installation/Extension.php`, so the answer's schema and `D-ANS-009`'s tests
are what the shape is held to.
