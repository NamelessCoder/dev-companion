---
date: 2026-08-25T10:53:00+00:00
category: missing-knowledge
status: closed
closed: 2026-08-27
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3_component_lookup, typo3_fluid_namespace_list
directory: /home/benji/projects/typo3-cms
---

# DocumentationViewHelper is the right way to link docs from a backend template and nothing says so

## Observation

Task: mid-session the user wrote "wir sollten links in templates durch DocumentationViewHelper ersetzen", with `typo3/sysext/install/Resources/Private/Templates/Upgrade/ExtensionScanner.fluid.html` open — a template the patch had just given a hardcoded `<a href="https://docs.typo3.org/permalink/...">`.

They were right, and the reason is a rule nothing in this server states: `TYPO3\CMS\Backend\ViewHelpers\Link\DocumentationViewHelper` renders the link through `Typo3Information::getDocsLink()`, which appends `@<branch>` from `Typo3Version`, so the reader lands on the documentation of the TYPO3 version they are running instead of on main. In a backend or install-tool template that is strictly better than a bare permalink, and it also removes the version question from a backport — which is exactly what the open review thread on the change was arguing about.

Everything I needed I read out of the checkout, in five greps and two file reads:
- the ViewHelper class and its docblock, which says it deliberately avoids DI because it is used in the install tool;
- its functional test, which proves `target` and `rel` are forced and any caller-supplied values are overridden, so those attributes come out of the markup;
- `ViewHelperResolver::createViewHelperInstanceFromClassName()`, whose FailsafeContainer branch goes through `makeInstance()`, which is why a ViewHelper without constructor arguments needs no entry in `install/Classes/ServiceProvider.php` — that file wires only four;
- `Configuration/Fluid/Namespaces.php` across all system extensions, to establish that `be` is not globally registered and has to be declared per template;
- the existing precedents for both declaration forms, `xmlns:be` on an `<html>` root and `{namespace be=TYPO3\CMS\Backend\ViewHelpers}` as line one where there is none, plus the dominant attribute ordering.

I did not put any of that to the server. I assumed `typo3_component_lookup` was for backend markup and CSS classes rather than ViewHelpers, and I answered the "is `be` globally registered" question by grepping instead of calling `typo3_fluid_namespace_list`, which is described as answering exactly that. The grep gave the right answer, so the assumption cost a round trip rather than an error — but `typo3_fluid_namespace_list` is a tool I read in the list and passed over, which is the kind of thing only a session can report.

The nearest thing the server did give me was in `typo3_task_guide`'s hints: `fluid-viewhelpers` and `fluid-templates`. Both are about writing a ViewHelper and about template resolution order. Neither says which core ViewHelper to reach for when a template needs a documentation link, and neither mentions the install-tool failsafe constraint on ViewHelpers — although the skill base text and AGENTS.md both do, in general terms.

## Query

Mid-session instruction "wir sollten links in templates durch DocumentationViewHelper ersetzen" while editing install-tool Fluid templates on 15.0.0-dev. No typo3_* call made; answered from the checkout with grep and file reads. typo3_task_guide had earlier returned hints "fluid-viewhelpers" and "fluid-templates" for these paths, neither of which covers it.

## Suggestion

A hint under the Fluid or Backend category, reachable from paths like `typo3/sysext/*/Resources/Private/Templates/*.fluid.html`, saying:

- A link into the official documentation from a backend or install-tool template is `<be:link.documentation identifier="t3coreapi:...">`, not a hardcoded `<a href>`. It resolves through `Typo3Information::getDocsLink()`, which appends `@<branch>`, so the link follows the installation's version and survives a backport unchanged.
- The ViewHelper forces `target="_blank"` and `rel="noreferrer"`; passing either is silently overridden, so leave them out.
- `be` is not among the globally registered prefixes. Declare `xmlns:be="http://typo3.org/ns/TYPO3/CMS/Backend/ViewHelpers"` on the `<html>` root, or `{namespace be=TYPO3\CMS\Backend\ViewHelpers}` as the first line where the template has no root element (a layout-only template, for instance).
- It works in the install tool because it has no constructor arguments: `ViewHelperResolver` takes the `makeInstance()` route under the FailsafeContainer. A ViewHelper that gains required constructor arguments has to be wired in `install/Classes/ServiceProvider.php` first.

Same for `PHP`: a docs link in a docblock or an exception message is a permalink, and `Typo3Information::getDocsLink()` is how code builds one.
