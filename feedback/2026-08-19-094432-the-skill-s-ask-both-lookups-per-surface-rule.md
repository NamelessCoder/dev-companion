---
date: 2026-08-19T09:44:32+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3-extension-health, typo3_icon_lookup, typo3_label_lookup, typo3_backend_module_lookup, typo3_fluid_namespace_list, typo3_configuration_lookup, typo3_documentation_lookup
directory: /home/benji/projects/blog
---

# The skill's "ask both lookups per surface" rule lost against typo3_extension_describe, and I skip...

## Observation

Task: full audit of the blog extension before its v14 release, run through the typo3-extension-health skill.

The skill is emphatic that a surface is not assessed until two kinds of lookup were asked: the runtime one that says what is registered, and the conventions one that says whether it is right. "A surface can be confirmed by its own runtime lookup and still break every rule that governs it." It names typo3_backend_module_lookup, typo3_icon_lookup, typo3_label_lookup, typo3_fluid_namespace_list and typo3_configuration_lookup as the runtime half.

I called none of those five. I also loaded typo3_documentation_lookup's schema via ToolSearch and never called it. Six tools read and passed over — reporting it because the server sees the calls that were made and nothing else, so from its side this session simply looks like a caller who did not need them.

Why: typo3_extension_describe had already returned the backend modules (4), the icons (24 identifiers), the XLF files with their source languages, the site sets with their files, and — notably — fluidNamespaces: [] for an extension that does register a global namespace. That last one was a signal I read correctly, but only because I later found the registration in ext_localconf.php myself. I treated the describe output as the runtime half for every one of those surfaces and moved on. It was enough to produce findings, and the audit did not visibly suffer, but I cannot claim I followed the skill.

The honest reading is that typo3_extension_describe has grown into a better first answer than the per-surface runtime lookups for an extension-scoped audit, and the skill has not caught up. Its own step 2 already tells you to call it. Once you have, calling typo3_icon_lookup for icons the describe just listed feels like re-asking, and calling typo3_label_lookup requires having a specific label in hand, which an audit does not until it finds something suspicious.

The one that would genuinely have added something is typo3_configuration_lookup. The extension registers a FormEngine data provider into SYS/formEngine/formDataGroup/tcaDatabaseRecord with depends and after, and the tool's own description says asking for that path also returns the order providers actually run in. I had that exact question, read the registration in ext_localconf.php, judged it plausible and moved on. That surface is unassessed in my report and I said so, but I could have settled it in one call. My assumption — that reading the registration was equivalent — did not hold, because what the registration declares and what the installation resolves are different things, which is precisely what that tool exists to say.

typo3_documentation_lookup: never called. The base names it for "does this still work in version N". My version questions were all "since when does this exist", which the manual cannot answer by page title. So the tool was right to stay unused for those; but I never tested whether it could answer any of them, and I should say that rather than claim it would not have.

## Query

Skill typo3-extension-health, section "Ask before judging, on every surface in scope". Called: typo3_extension_describe, typo3_hint_lookup x5, typo3_changelog_lookup x12, typo3_rule_lookup x1, typo3_commit_message_guide x5. Not called: typo3_icon_lookup, typo3_label_lookup, typo3_backend_module_lookup, typo3_fluid_namespace_list, typo3_configuration_lookup, typo3_documentation_lookup, typo3_component_lookup, typo3_schema_lookup, typo3_test_run_guide.

## Suggestion

Reconcile the skill with what typo3_extension_describe now returns. Either say plainly that for an extension-scoped audit the describe call is the runtime half for modules, icons, site sets and language files, and name the surfaces it does not cover — or say why the per-surface lookups still earn their round trip after it. As written, the rule is followed by nobody who has already called step 2, and a rule that is routinely skipped teaches that the next one can be too.

Two surfaces do look genuinely uncovered by describe and worth naming as such:
- effective TYPO3_CONF_VARS, via typo3_configuration_lookup — describe reports what the extension registers, not what the installation resolved. The formDataGroup ordering case is the example.
- globally registered Fluid namespaces — describe returned fluidNamespaces: [] while the extension does register one through TYPO3_CONF_VARS. Either that field should reflect the legacy registration, or the skill should say that an empty list there is not evidence of absence.
