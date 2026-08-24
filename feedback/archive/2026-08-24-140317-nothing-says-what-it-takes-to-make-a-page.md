---
date: 2026-08-24T14:03:17+00:00
category: missing-knowledge
status: closed
closed: 2026-08-24
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3-development-installation
directory: /home/benji/projects/ext-usercentrics
---

# Nothing says what it takes to make a page render for an extension that ships no page TypoScript

## Observation

Task: make the frontend of a consent-management extension visible in a browser, so its rendered script tags could be checked, TYPO3 13.4 and 14.3.

The extension ships a site set with settings definitions and no setup.typoscript, which is correct — a consent extension must not define a page object. So a development installation of it has nothing that renders a page, and typo3 setup --create-site produces a root page but the site set contributes no page TypoScript.

That single question drove three architecture reversals across the session:

1. First I seeded a pages row and a sys_template row with raw SQL from a shell script. Rejected by the user: "wir wollen auch keine seed ssh dateien haben".
2. Then a development-only extension under Build/ carrying a site set with page = PAGE and a Fluid template, plus site configuration templates copied into place by DDEV post-start hooks. The user rejected the supporting folder: "den devEnvironment ordner find ich auch nicht gut".
3. Finally: container declaration only, no hooks, the demo extension kept, install steps documented and executed by hand.

None of the hints I read covered the question. site-sets describes what a set is and what files it may hold. extension-repository-installation covers the Composer layout. installation-setup covers the setup command. fresh-instance-seeding, which I did not call, is described as "the ways a package declares one" and reads as being about distributions and Initialisation/data.xml — the answer for a package that fills an instance, not for a package that needs an instance to have a page at all.

The general shape I ended at is probably the right one and is worth writing down: for an extension that renders into a page but does not define one, the development installation needs a page object from somewhere, and a development-only extension under Build/ shipping a site set is the way to provide it without putting demo TypoScript into the released package — Build/ is already export-ignored. Its directory has to be named after its extension key in lower case, which I got wrong first and was corrected on ("extension namen für ordner sind immer lowercased").

## Query

typo3_hint_lookup id=extension-repository-installation, id=installation-setup, id=project-configuration-files; skill typo3-development-installation with args "Set up a DDEV based development installation for the ext-usercentrics extension so the site set can be verified in a real frontend". No hint covered how a page comes to render for an extension shipping no page TypoScript.

## Suggestion

A hint of its own, reachable by a query like "development installation page does not render" or "extension ships no page TypoScript": an extension that renders into a page but defines none needs the development installation to supply one; the options are a sys_template record, a site-level setup.typoscript, or a development-only extension under Build/ whose site set carries it, and only the last stays out of the released package and out of the way of --create-site. Say that the extension directory is named after its extension key in lower case, and that Build/ being export-ignored is what keeps it unreleased. Cross-reference it from typo3-development-installation's "Seed the content the package is to be developed against" step, which currently routes only to fresh-instance-seeding and so reads as being about distributions.
