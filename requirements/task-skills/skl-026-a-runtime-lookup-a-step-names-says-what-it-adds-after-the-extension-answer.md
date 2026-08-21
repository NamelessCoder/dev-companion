---
id: R-SKL-026
status: open
restsOn: [D-SKL-069]
---

# R-SKL-026 — A runtime lookup a step names says what it adds after the extension answer

**Where a step names a runtime lookup, it says what that lookup adds after the
`typo3_extension_describe` call the order has already made.**

The two are not the same question. The extension answer says what one package
registers, read from its own files and from the booted installation for the
tables, content elements and icons it is attributed; a runtime lookup says what
the installation resolved — the value every extension has had its say on, the
identifier whatever package registered it, the label with the overrides applied,
the module tree position the parent supplies. A step that names the second
without saying so is asking for a call the reader has just made.

Naming what it adds is what makes the call survive the reading. A session that
has the identifiers, the modules and the language files in hand from step 2 will
not spend five round trips on tools introduced as reporting what is registered,
and it will report those surfaces as assessed off a registration list.

## From

The feedback of 2026-08-19 09:44, from a full audit of a blog extension before
its v14 release. `typo3_backend_module_lookup`, `typo3_icon_lookup`,
`typo3_label_lookup`, `typo3_fluid_namespace_list` and
`typo3_configuration_lookup` were all named by the active skill and none was
called, because `typo3_extension_describe` had already returned the modules, the
icons, the site sets and the XLF files. The one that would have added something
was `typo3_configuration_lookup`: the extension registers a FormEngine data
provider with `depends` and `after`, and the run judged the order off the
registration in `ext_localconf.php` rather than asking the installation what it
resolved. `D-SKL-069` is the judgement, and it puts the wording in
`skills/base.md`, which every published skill carries a copy of.

## Held by

- not guarded
