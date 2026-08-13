---
date: 2026-08-13T21:56:52+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_hint_lookup
directory: /home/benji/projects/typo3-cms
---

# a new XLF trans-unit compiles into the ~labels types but throws at runtime until cache:flush and ...

## Observation

Audit of an agent's own accumulated project notes against what this server answers. This one survived, and it is the note that most often saves a session from debugging the wrong file.

The note, established while adding job_queue.* labels to a backend TypeScript module: when a new <trans-unit> is added to an XLF that backs a JavaScript label bundle (~labels/<domain>, e.g. ~labels/core.core from locallang_core.xlf), the grunt build regenerates only the TYPES, in Build/types/labels/*.d.ts. So the TypeScript compiles, the editor autocompletes the new key, lint passes, the build is green — and at runtime LabelProvider.get('new.key') throws "Error: Label is not defined: new.key".

The reason is that the two sides are produced by different things. The ~labels/* module is generated server-side from the XLF by JavaScriptLanguageDomainProvider::createLanguageDomainResponse() and served with a Cache-Control max-age of one year, and the parsed XLF additionally sits in TYPO3's language cache. The type stub is a build artefact; the module the browser runs is an HTTP response with a year on it.

So the fix is two steps and both are needed: `bin/typo3 cache:flush` to invalidate the language cache so the bundle is regenerated, AND a browser hard-reload (Ctrl+Shift+R) to get past the one-year HTTP cache on the label module the page already fetched. Doing only the first leaves the browser on the old bundle and the error unchanged, which is what makes this look like a code problem for as long as it does.

What the server answered instead: typo3_hint_lookup with the task above returned language-files, backend-typescript and backend-ui. language-files is thorough on authoring, locale prefixes, the target-language trap and the XLIFF linter, and says nothing about the JavaScript side. backend-typescript covers the other half of the same gap from the opposite direction — "The generated file is committed, so a source change that is not built leaves the two disagreeing and the browser running the old one" — which is the same category of failure for TypeScript output and is stated, while the label bundle's version of it is not.

## Query

typo3_hint_lookup(task: "JavaScript labels module cache flush after adding XLF trans-unit")

## Suggestion

Add it to language-files, or to backend-typescript beside the sentence about generated JavaScript going stale, which is its exact twin.

The three things that make it actionable: that only the .d.ts is rebuilt so every build-time check stays green; the runtime error string "Label is not defined: <key>", which is what somebody will be searching with; and both halves of the fix, because cache:flush alone does not clear the one-year HTTP cache and a session that does only that concludes the flush did not help.

Worth naming JavaScriptLanguageDomainProvider::createLanguageDomainResponse() as where the bundle comes from — it is what makes the year-long max-age findable rather than mysterious.
