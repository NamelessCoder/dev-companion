---
date: 2026-08-03T14:44:57+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_documentation_lookup, typo3_changelog_lookup, typo3_hint_lookup
directory: /home/benji/projects/typo3-cms
---

# Task: review core commit 9f6c6eb9093 "[TASK] Resolve file sources in ResourceFactory" and report ...

## Observation

Task: review core commit 9f6c6eb9093 "[TASK] Resolve file sources in ResourceFactory" and report what blocks it.

Three questions decided findings in this review and none was answered by a server call. Item 3 is properly checkout work and I am not asking for it. Items 1 and 2 are the ones worth looking at.

(1) "Does the /typo3/ URL prefix still exist on main?" This is the load-bearing question of the whole review — the commit removes user-visible backend behaviour on the strength of "the /typo3/ directory ... does not exist since v13". It is a "does this still hold in version N" question, which base.md routes to typo3_documentation_lookup. I did not call it; I went to typo3_changelog_lookup, got matchCount 0 with no narrowing (filed separately as a bug), and then grepped. The skip is mine. But it is worth recording what the right call would have had to answer, because I am not confident it could: "is /typo3/ still the backend request context in 15.0" is a property of the installed code, not of the manual, and base.md's own two-kinds-of-lookup section says the runtime lookups establish facts while documentation says whether they are right. There is no runtime lookup for "what URL prefix does backend routing use" — typo3_backend_module_lookup covers registered modules, not the router's context. So a caller asking a question of exactly this shape has, as far as I can tell, no server tool that answers it, and the checkout is the honest fallback. Recording it so somebody can check whether typo3_documentation_lookup would in fact have answered, and route it explicitly if not.

(2) "Is #[Autowire(lazy: true)] an established core pattern?" I recommended it as the fix for the new eager LinkService dependency on ResourceFactory, and a recommendation to a core reviewer needs precedent, not taste. Three grep hits gave me that. No lookup I made would have: typo3_task_guide's hint groups for these paths were fluid-viewhelpers, system-extension-boundaries, core-tests and fal-basics — nothing on dependency injection or service wiring, because none of the changed paths reads as DI configuration. typo3_changelog_lookup would only have it if a changelog entry announced lazy autowiring, which is a change-event question rather than a "what does core do today" question.

The pattern behind both: this server is strong on what the project REQUIRES (rules, changelog obligations, test invocations, subsystem conventions) and on what is REGISTERED (modules, icons, labels, sites). It is thin on "what does core code currently do, and is this idiom established" — which is the question a reviewer asks constantly when deciding whether a proposed alternative is a real suggestion or a personal preference. Every finding in my report where I proposed an alternative needed that, and every one of them I answered by grep.

I am not claiming this is a gap the server must fill — a grep over the checkout is cheap and the server explicitly does not read the working tree. What I am claiming is that a caller has no way to know in advance that these questions have no home here, so they cost a failed lookup first.

## Query

Questions raised during the review of core commit 9f6c6eb9093 (#110359) that no server call answered, settled by grepping the 15.0.0-dev checkout instead: (1) does the /typo3/ URL prefix still exist on main — settled at typo3/sysext/backend/Classes/Routing/UriBuilder.php:199, `new RequestContext('/typo3/')`; (2) is `#[Autowire(lazy: true)]` an established core pattern — settled at core/Classes/Site/Set/SetRegistry.php:43, form/Classes/EventListener/DataStructureIdentifierListener.php:68, form/Classes/Domain/Configuration/PersistenceConfigurationService.php:41; (3) does LinkService construct its handlers eagerly — settled at core/Classes/LinkHandling/LinkService.php:56-71 against core/Configuration/DefaultConfiguration.php:535-543.

## Suggestion

Two cheap things. First, have typo3_server_scope (and ideally the base.md two-kinds-of-lookup section) state the third category explicitly: "what the project requires" and "what this installation registers" are covered; "what core code currently does, and whether an idiom is established in it" is not, and is grep over the checkout. Naming the boundary is worth more than filling it, because it stops the failed lookup. Second, if any of it is to be filled, the highest-value slice for patch review is an idiom-precedent lookup — given a PHP attribute, class or call (`#[Autowire(lazy: true)]`, `SingletonInterface`, `#[AsEventListener]`), return the core call sites that use it with paths and line numbers, from the installed core package rather than the working tree. That is exactly the evidence a review needs to turn a suggestion into a precedented one, and it is the same "read the installed package" mechanism typo3_extension_scope already uses.
