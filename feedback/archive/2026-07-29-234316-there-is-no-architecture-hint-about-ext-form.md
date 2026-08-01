---
date: 2026-07-29T23:43:16+00:00
category: missing-knowledge
status: closed
closed: 2026-07-30
commit: 2f9b25e
subject: "[FEATURE] Cover EXT:form configuration and runtime"
tool: typo3_architecture_lookup, typo3_task_guide
directory: /home/benji/projects/site-new
---

# There is no architecture hint about EXT:form anywhere in the catalog. The full id list returned b...

## Observation

There is no architecture hint about EXT:form anywhere in the catalog. The full id list returned by an unmatched id request (57 hints) contains nothing about the form framework — no form sets, no form definitions, no prototype configuration, no finishers, no prefilling. Both calls answered with sitepackage-layout, frontend-page-rendering and site-sets instead, which are correct for "a sitepackage" and say nothing about the actual task. Everything needed had to be read out of vendor/typo3/cms-form and the changelog by hand, and the two decisions that were easy to get wrong were both invisible from the outside:

1. Where a form definition and its registration live in an extension. Since v14.2 (#109412) it is Configuration/Form/<SetName>/config.yaml, auto-discovered, with persistenceManager.allowedExtensionPaths pointing at Resources/Private/Forms/, and the definition file must end in ".form.yaml" (FormPersistenceManagerInterface::FORM_DEFINITION_FILE_EXTENSION). The predecessor — plugin.tx_form.settings.yamlConfigurations plus a second registration under module.tx_form.settings.yamlConfigurations — is deprecated for removal in v15, and it is what every older tutorial still shows. This is exactly the shape a sitepackage hint already describes for site sets, and the parallel is explicit in the changelog ("mirrors how Site Sets work").

2. That the form identifier is not stable at runtime. FormFrontendController::renderAction() appends "-" plus the uid of the content element to $formDefinition['identifier'] and keeps the declared one in renderingOptions._originalIdentifier. Any listener or override that matches on FormDefinition::getIdentifier() therefore never matches in the frontend while matching fine in a unit test — a failure that only the real frontend shows.

Related, and the reason a hint is worth having: v14.2 also deprecated file-mount form storage (#108653) in favour of records in form_definition, but explicitly exempted allowedExtensionPaths. So "which storage does a sitepackage use for a version-controlled form" now has three candidates, two of which are wrong, and the answer is only in the note at the bottom of a deprecation entry.

## Query

typo3_architecture_lookup task="EXT:form form definition YAML, form setup in sitepackage, prefill form fields" targetVersion="14.3"; typo3_task_guide task="Add an EXT:form form definition to a sitepackage and prefill a form field with a product number from the URL" area="form" targetVersion="14.3"

## Suggestion

Add an architecture hint for the form framework — id something like "form-framework" or "ext-form-configuration", category General or TypoScript — covering: the Configuration/Form/<SetName>/config.yaml auto-discovery layout and its name/label/priority metadata (priority 10 is the core base set typo3/form-base, extension sets go above it, default 100); that the deprecated TypoScript registration needed two entries and the new mechanism needs none; the ".form.yaml" suffix requirement; allowedExtensionPaths as the storage for forms an extension ships read-only versus form_definition records for editable ones, with the v15 removal of allowedFileMounts; the identifier suffixing in renderAction() and _originalIdentifier as the thing to match on; the four form.templates.* / form.translation.* site set settings from the typo3/form set; and that plugin.tx_form.settings.formDefinitionOverrides still exists, is keyed on the unsuffixed identifier, and merges with mergeRecursiveWithOverrule — so a map like a finisher's "recipients" gets added to rather than replaced.
