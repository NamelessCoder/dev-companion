# Say where runTests.sh stops reading its own options

**Serves:** feedback/2026-08-24-122308-typo3-test-run-guide-stayed-shut-and-i-burned-a.md
**Priority:** normal

Judged on 2026-08-24 as the ladder's step 1a and written up in `D-KNW-112`: the
tool does answer argument order and the session never loaded its description, so
the missing half is the corpus rather than the wording. The reading the todo
would otherwise start with is in the entry — `getopts` stops at the first
non-option word on all four covered branches, and `shift $((OPTIND - 1))` sends
the rest to phpunit.

Write that into `invocation.notes` in `knowledge/test-suite-hints.json`, beside
the note that says everything after `--` is handed on unchanged: the rule rather
than the symptom, so it covers a `-d` written after a path as well as a path
written before the separator, and phrased so a session that has already read
`Test file "--filter" not found` recognises it. Run `bin/cli knowledge:format`
after.

Normal rather than low because the reading is done and what is left is one note,
against a cost the reporting session measured at a container cycle and a failed
suite.
