---
date: 2026-08-13T21:49:27+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_server_scope, typo3_project_describe, typo3-core-patch-review
directory: /home/benji/projects/typo3-cms
---

# what I never asked this server, and the two review surfaces I answered from the checkout without ...

## Observation

Task: review Gerrit change 93319 (Playwright e2e specs for the page wizard). This reports what I did not put to the server, since it can see only the calls that were made.

Never called, and the assumption behind each:

- typo3_server_scope. My client did not show me a resource list, so the only place the bundled procedures were named to me was the tail of typo3_project_describe, which listed ten guide ids. That listing was enough and I never asked what else was covered or at what depth. The assumption — that project_describe's guide list is the whole of what is readable by id — held for this task but I did not check it.
- typo3_component_lookup. The diff asserts on backend markup: .wizard-loader, .form-check-type-card, .node-name, #tracker-details, [role="treeitem"], .wizard-actions button. That is exactly the surface the server instructions say to call it for before writing backend markup or CSS classes. I grepped Build/Sources/TypeScript instead, every time, without pausing over it. My reasoning, unexamined at the time: a curated component catalogue answers "what is the right class to use", and I needed "does this class literally exist in the element this spec drives" — which is a source question. It held: the grep found all six at file and line. But I would make the same call again without considering the tool, which is the finding.
- typo3_changelog_lookup. The base order's deprecation sweep has an explicit skip for a change touching no TYPO3 API, and a Playwright-only diff is squarely in it. I skipped it deliberately and would again. Recording it so the absence is not read as an oversight.
- typo3_documentation_lookup. The diff changes no documented behaviour. Not owed.
- typo3_icon_lookup, typo3_schema_lookup, typo3_configuration_lookup, typo3_backend_module_lookup, typo3_fluid_namespace_list, typo3_translation_domain_lookup, typo3_extension_describe, typo3_hint_lookup as a separate call. Each either not applicable to a test-only diff or already answered — hint_lookup by task_guide's own coverage statement, extension_describe by project_describe reporting a core checkout with no project-owned extensions.

Two review surfaces I answered from the checkout without asking whether the server could:

1. Backport viability. The message says "Releases: main, 14.3". Whether the backport is meaningful depends on whether 14.3 has the page wizard and the fixture at all, and whether the methods this patch deletes have callers there. I answered it with git ls-tree and git grep against origin/14.3. It never occurred to me to ask the server, and I think correctly — it does not read my working tree and 14.3 is a branch in it. But "does branch X still have symbol Y" is a question a reviewer asks on every backport-carrying change, and the base order says establishing it is mine. Worth stating that the answer landed cleanly: 14.3 has both, and neither deleted method has a caller there.

2. Whether the deleted fixture methods were already dead. `git grep` at HEAD~1 answered it in one call. Same reasoning, same conclusion.

One thing this debrief did not ask about and I would report anyway: the review skill's boundary against rewriting what it reviews held under real pressure. I found four things I could have "just fixed" — a missing isReady(), a discarded return value, a dropped assertion, trailing blank lines — and the skill's warning that a session crossing that line looks like nothing from the inside is the reason I did not. It names the failure mode concretely enough to recognise while it is happening, which is rarer than it sounds. Keep it.

## Query

Whole session: review Gerrit change 93319 patch set 21 after cherry-picking onto main. Reporting the calls not made.

## Suggestion

Consider saying in typo3_component_lookup's description that it answers "what is the registered/curated component" and not "does this selector exist in the current source", and name grepping the checkout as the right move for the second. A session driving backend markup from a Playwright spec is in the second case every time, and the server-level instruction ("before writing backend markup or CSS classes, call typo3_component_lookup") reads as covering it.

Consider whether typo3_server_scope should be reachable from somewhere a session actually looks. project_describe's guide list is what I found; scope was never in my path, and a client that renders no resource list makes that the only route.

Nothing to change about the deprecation-sweep skip or the review skill's rewrite boundary. Both worked as written.
