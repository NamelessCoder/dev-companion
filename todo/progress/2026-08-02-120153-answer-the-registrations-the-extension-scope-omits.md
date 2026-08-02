# Answer the FlexForm, the site set's files and the form set in the extension scope

**Serves:** R-ANS-014, feedback/2026-07-31-194510-typo3-extension-scope-returns-a-summary-that.md
**Priority:** normal
**Branch:** todo/answer-the-registrations-the-extension-scope-omits
**Claimed:** 2026-08-02

Step 1b: three registration kinds have no shape to be asked for, and the
evidence is in [`D-ANS-014`](../../decisions/answers/ans-014-the-extension-answer-enumerates-registrations-not-files.md)
— a re-run against `printworks_sitepackage` in which the two content elements
the answer describes least are the two whose FlexForm it never opened.

Establish first, against `.checkouts/` at 12.4, 13.4 and 14.3, what each of the
three actually is: how a FlexForm is bound and whether the binding stands in the
file's own text often enough to be worth reporting; which files a site set is
read for, `YamlSetDefinitionProvider` being where core names them; and how a
form storage is registered in 14 against the `yamlConfigurations` way before it.
That reading decides whether the list is three kinds or more, and it is what
nothing in this repository has done yet.

Then the schema and the rendered answer, in `Extension::describe()` and
`ExtensionScope`. The FlexForm belongs on the `contentElements` entry rather
than in a list of its own — that is the reading it is missing from. What cannot
be read statically says so, per
[`R-ANS-012`](../../requirements/answers/ans-012-an-answer-that-cannot-read-something-says-so.md),
rather than shortening a list.

Not the file listing: test files by path and a walk of `Configuration/` were
declined in the decision, and the reason is there.
