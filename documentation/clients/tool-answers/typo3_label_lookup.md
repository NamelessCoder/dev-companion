# What `typo3_label_lookup` answered

Recorded on 2026-08-02 by `bin/cli tools:record`. Of two working directories,
because what this server answers depends on which one a client is standing in,
and neither fills the whole surface. Answered against core-checkout, TYPO3
14.3.6-dev, the 14.3 core checkout below .checkouts/, whose console could not
be reached: <installation> has no TYPO3 console — none of bin/typo3,
vendor/bin/typo3 exists. Answered against composer-project, TYPO3 14.3.5, the
E-SITE this repository makes below .environments/, whose console answers. The
tools that declare `answeredBy` carry an answer from each, under a heading
naming which; every other answer is from the first alone, because nothing in it
would differ. Nothing checks this page; [tools.md](../tools.md) is where the
current shape of an answer is, and [readme.md](readme.md) is what the recording
as a whole is of.

## labels: hit

Called with:

```json
{
    "query": "save"
}
```

### From the 14.3 core checkout below .checkouts/, whose console could not be reached

Text:

```
106 label(s) in <installation> match "save" — showing the first 25:
- backend.alt_doc:buttons.confirm.duplicate_record_changed.yes
  "Yes, save and duplicate this record"
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:buttons.confirm.close_without_save.yes
  "Discard changes"
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:buttons.confirm.close_without_save.no
  "Keep editing"
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:buttons.confirm.save_and_close
  "Save and close"
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:label.confirm.duplicate_record_changed.title
  "Do you want to save before duplicating this record?"
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:label.confirm.duplicate_record_changed.content
  "You currently have unsaved changes. Do you want to save your changes before duplicating this record?"
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:label.confirm.new_record_changed.title
  "Do you want to save before adding?"
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:label.confirm.new_record_changed.content
  "You need to save your changes before creating a new record. Do you want to save and create now?"
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:buttons.confirm.new_record_changed.yes
  "Yes, save and create now"
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:label.confirm.view_record_changed.title
  "Do you want to save before viewing?"
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:label.confirm.view_record_changed.content
  "You currently have unsaved changes. You can either discard these changes or save and view them."
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:label.confirm.view_record_changed.invalid_form
  "The form appears to be invalid, therefore "Save changes and view" is not available."
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:label.confirm.view_record_changed.content.is-new-page
  "You need to save your changes before viewing the page. Do you want to save and view them now?"
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:buttons.confirm.view_record_changed.no-save
  "View without changes"
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:buttons.confirm.view_record_changed.save
  "Save changes and view"
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:label.confirm.close_without_save.title
  "Unsaved changes"
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:label.confirm.close_without_save.content
  "You currently have unsaved changes which will be discarded if you close without saving."
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:label.alert.save_with_error.title
  "You have errors in your form!"
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:label.alert.save_with_error.content
  "Please check the form, there is at least one error in your form."
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:buttons.alert.save_with_error.ok
  "OK"
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:notification.record_saved.title.singular
  "Record saved"
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:notification.record_saved.title.plural
  "Records saved"
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:notification.record_saved.message
  "Record "%s" has been successfully saved."
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:notification.mass_saving.message
  "%s records have been successfully saved."
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.mfa:save.failure
  "Could not update MFA provider %s. Please try again."
  EXT:backend/Resources/Private/Language/locallang_mfa.xlf

Reference a label by the domain form shown first (package.resource:key) — in TCA, in LanguageService::sL(), and in f:translate as separate domain and key attributes.

A match is reusable only when its resource is the one already used at the consuming code. A label from another module or package is not a shared vocabulary merely because its text matches; call again with resource once that usage context is known.

Read from the XLF files of the installed packages: the console could not be asked (<installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists). What that leaves out is the assembled runtime state — a label an installation replaces through LANG/resourceOverrides is shown here as its package ships it.
```

Data:

```json
{
    "query": "save",
    "resource": null,
    "matchCount": 106,
    "labels": [
        {
            "ref": "backend.alt_doc:buttons.confirm.duplicate_record_changed.yes",
            "domain": "backend.alt_doc",
            "key": "buttons.confirm.duplicate_record_changed.yes",
            "source": "Yes, save and duplicate this record",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.alt_doc:buttons.confirm.close_without_save.yes",
            "domain": "backend.alt_doc",
            "key": "buttons.confirm.close_without_save.yes",
            "source": "Discard changes",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.alt_doc:buttons.confirm.close_without_save.no",
            "domain": "backend.alt_doc",
            "key": "buttons.confirm.close_without_save.no",
            "source": "Keep editing",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.alt_doc:buttons.confirm.save_and_close",
            "domain": "backend.alt_doc",
            "key": "buttons.confirm.save_and_close",
            "source": "Save and close",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.alt_doc:label.confirm.duplicate_record_changed.title",
            "domain": "backend.alt_doc",
            "key": "label.confirm.duplicate_record_changed.title",
            "source": "Do you want to save before duplicating this record?",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.alt_doc:label.confirm.duplicate_record_changed.content",
            "domain": "backend.alt_doc",
            "key": "label.confirm.duplicate_record_changed.content",
            "source": "You currently have unsaved changes. Do you want to save your changes before duplicating this record?",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.alt_doc:label.confirm.new_record_changed.title",
            "domain": "backend.alt_doc",
            "key": "label.confirm.new_record_changed.title",
            "source": "Do you want to save before adding?",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.alt_doc:label.confirm.new_record_changed.content",
            "domain": "backend.alt_doc",
            "key": "label.confirm.new_record_changed.content",
            "source": "You need to save your changes before creating a new record. Do you want to save and create now?",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.alt_doc:buttons.confirm.new_record_changed.yes",
            "domain": "backend.alt_doc",
            "key": "buttons.confirm.new_record_changed.yes",
            "source": "Yes, save and create now",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.alt_doc:label.confirm.view_record_changed.title",
            "domain": "backend.alt_doc",
            "key": "label.confirm.view_record_changed.title",
            "source": "Do you want to save before viewing?",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.alt_doc:label.confirm.view_record_changed.content",
            "domain": "backend.alt_doc",
            "key": "label.confirm.view_record_changed.content",
            "source": "You currently have unsaved changes. You can either discard these changes or save and view them.",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.alt_doc:label.confirm.view_record_changed.invalid_form",
            "domain": "backend.alt_doc",
            "key": "label.confirm.view_record_changed.invalid_form",
            "source": "The form appears to be invalid, therefore \"Save changes and view\" is not available.",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.alt_doc:label.confirm.view_record_changed.content.is-new-page",
            "domain": "backend.alt_doc",
            "key": "label.confirm.view_record_changed.content.is-new-page",
            "source": "You need to save your changes before viewing the page. Do you want to save and view them now?",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.alt_doc:buttons.confirm.view_record_changed.no-save",
            "domain": "backend.alt_doc",
            "key": "buttons.confirm.view_record_changed.no-save",
            "source": "View without changes",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.alt_doc:buttons.confirm.view_record_changed.save",
            "domain": "backend.alt_doc",
            "key": "buttons.confirm.view_record_changed.save",
            "source": "Save changes and view",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.alt_doc:label.confirm.close_without_save.title",
            "domain": "backend.alt_doc",
            "key": "label.confirm.close_without_save.title",
            "source": "Unsaved changes",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.alt_doc:label.confirm.close_without_save.content",
            "domain": "backend.alt_doc",
            "key": "label.confirm.close_without_save.content",
            "source": "You currently have unsaved changes which will be discarded if you close without saving.",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.alt_doc:label.alert.save_with_error.title",
            "domain": "backend.alt_doc",
            "key": "label.alert.save_with_error.title",
            "source": "You have errors in your form!",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.alt_doc:label.alert.save_with_error.content",
            "domain": "backend.alt_doc",
            "key": "label.alert.save_with_error.content",
            "source": "Please check the form, there is at least one error in your form.",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.alt_doc:buttons.alert.save_with_error.ok",
            "domain": "backend.alt_doc",
            "key": "buttons.alert.save_with_error.ok",
            "source": "OK",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.alt_doc:notification.record_saved.title.singular",
            "domain": "backend.alt_doc",
            "key": "notification.record_saved.title.singular",
            "source": "Record saved",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.alt_doc:notification.record_saved.title.plural",
            "domain": "backend.alt_doc",
            "key": "notification.record_saved.title.plural",
            "source": "Records saved",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.alt_doc:notification.record_saved.message",
            "domain": "backend.alt_doc",
            "key": "notification.record_saved.message",
            "source": "Record \"%s\" has been successfully saved.",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.alt_doc:notification.mass_saving.message",
            "domain": "backend.alt_doc",
            "key": "notification.mass_saving.message",
            "source": "%s records have been successfully saved.",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.mfa:save.failure",
            "domain": "backend.mfa",
            "key": "save.failure",
            "source": "Could not update MFA provider %s. Please try again.",
            "resource": "EXT:backend/Resources/Private/Language/locallang_mfa.xlf"
        }
    ],
    "terms": [
        {
            "term": "save",
            "matchCount": 106
        }
    ],
    "answeredBy": "packages"
}
```

### From the E-SITE this repository makes below .environments/, whose console answers

Text:

```
101 label(s) in <installation> match "save" — showing the first 25:
- core.file_messages:FileUtility.FileSavedTo
  "File saved to "%s"."
  EXT:core/Resources/Private/Language/fileMessages.xlf
- core.file_messages:FileUtility.FileWasNotSaved
  "File "%s" cannot be saved due to insufficent write permissions."
  EXT:core/Resources/Private/Language/fileMessages.xlf
- core.file_messages:FileUtility.FileWasNotSaved2.x-unused
  "File "%s" was not saved! File extension rejected!"
  EXT:core/Resources/Private/Language/fileMessages.xlf
- core.bookmarks:action.save
  "Save"
  EXT:core/Resources/Private/Language/locallang_bookmarks.xlf
- core.common:save
  "Save"
  EXT:core/Resources/Private/Language/locallang_common.xlf
- core.common:saveAndClose
  "Save and close"
  EXT:core/Resources/Private/Language/locallang_common.xlf
- core.common:saveAndCloseDoc
  "Save and close document"
  EXT:core/Resources/Private/Language/locallang_common.xlf
- core.common:saveAndCreateNewDoc
  "Save and create new document"
  EXT:core/Resources/Private/Language/locallang_common.xlf
- core.common:saveAndViewPage
  "Save and view page"
  EXT:core/Resources/Private/Language/locallang_common.xlf
- core.core:labels.fieldsChanged
  "Important: There are unsaved changes in the form. Continuing without saving will discard your changes. Continue?"
  EXT:core/Resources/Private/Language/locallang_core.xlf
- core.core:mess.refreshRequired.content
  "This change will affect which fields are available in the form. Would you like to save now in order to refresh the display?"
  EXT:core/Resources/Private/Language/locallang_core.xlf
- core.core:mess.refreshRequired.confirm
  "Save and refresh"
  EXT:core/Resources/Private/Language/locallang_core.xlf
- core.core:rm.saveDoc
  "Save"
  EXT:core/Resources/Private/Language/locallang_core.xlf
- core.core:rm.saveDocShow
  "Save and view page"
  EXT:core/Resources/Private/Language/locallang_core.xlf
- core.core:rm.saveCloseDoc
  "Save and close"
  EXT:core/Resources/Private/Language/locallang_core.xlf
- core.core:rm.saveCloseAllDocs
  "Save and close all"
  EXT:core/Resources/Private/Language/locallang_core.xlf
- core.core:rm.saveNewDoc
  "Save and create a new one"
  EXT:core/Resources/Private/Language/locallang_core.xlf
- core.core:cm.save
  "Save"
  EXT:core/Resources/Private/Language/locallang_core.xlf
- core.password_policy:dataHandler.passwordNotSaved
  "Password not saved ({table}:{uid}):"
  EXT:core/Resources/Private/Language/locallang_password_policy.xlf
- backend.alt_doc:buttons.confirm.duplicate_record_changed.yes
  "Yes, save and duplicate this record"
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:buttons.confirm.close_without_save.yes
  "Discard changes"
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:buttons.confirm.close_without_save.no
  "Keep editing"
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:buttons.confirm.save_and_close
  "Save and close"
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:label.confirm.duplicate_record_changed.title
  "Do you want to save before duplicating this record?"
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf
- backend.alt_doc:label.confirm.duplicate_record_changed.content
  "You currently have unsaved changes. Do you want to save your changes before duplicating this record?"
  EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf

Reference a label by the domain form shown first (package.resource:key) — in TCA, in LanguageService::sL(), and in f:translate as separate domain and key attributes.

A match is reusable only when its resource is the one already used at the consuming code. A label from another module or package is not a shared vocabulary merely because its text matches; call again with resource once that usage context is known.
```

Data:

```json
{
    "query": "save",
    "resource": null,
    "matchCount": 101,
    "labels": [
        {
            "ref": "core.file_messages:FileUtility.FileSavedTo",
            "domain": "core.file_messages",
            "key": "FileUtility.FileSavedTo",
            "source": "File saved to \"%s\".",
            "resource": "EXT:core/Resources/Private/Language/fileMessages.xlf"
        },
        {
            "ref": "core.file_messages:FileUtility.FileWasNotSaved",
            "domain": "core.file_messages",
            "key": "FileUtility.FileWasNotSaved",
            "source": "File \"%s\" cannot be saved due to insufficent write permissions.",
            "resource": "EXT:core/Resources/Private/Language/fileMessages.xlf"
        },
        {
            "ref": "core.file_messages:FileUtility.FileWasNotSaved2.x-unused",
            "domain": "core.file_messages",
            "key": "FileUtility.FileWasNotSaved2.x-unused",
            "source": "File \"%s\" was not saved! File extension rejected!",
            "resource": "EXT:core/Resources/Private/Language/fileMessages.xlf"
        },
        {
            "ref": "core.bookmarks:action.save",
            "domain": "core.bookmarks",
            "key": "action.save",
            "source": "Save",
            "resource": "EXT:core/Resources/Private/Language/locallang_bookmarks.xlf"
        },
        {
            "ref": "core.common:save",
            "domain": "core.common",
            "key": "save",
            "source": "Save",
            "resource": "EXT:core/Resources/Private/Language/locallang_common.xlf"
        },
        {
            "ref": "core.common:saveAndClose",
            "domain": "core.common",
            "key": "saveAndClose",
            "source": "Save and close",
            "resource": "EXT:core/Resources/Private/Language/locallang_common.xlf"
        },
        {
            "ref": "core.common:saveAndCloseDoc",
            "domain": "core.common",
            "key": "saveAndCloseDoc",
            "source": "Save and close document",
            "resource": "EXT:core/Resources/Private/Language/locallang_common.xlf"
        },
        {
            "ref": "core.common:saveAndCreateNewDoc",
            "domain": "core.common",
            "key": "saveAndCreateNewDoc",
            "source": "Save and create new document",
            "resource": "EXT:core/Resources/Private/Language/locallang_common.xlf"
        },
        {
            "ref": "core.common:saveAndViewPage",
            "domain": "core.common",
            "key": "saveAndViewPage",
            "source": "Save and view page",
            "resource": "EXT:core/Resources/Private/Language/locallang_common.xlf"
        },
        {
            "ref": "core.core:labels.fieldsChanged",
            "domain": "core.core",
            "key": "labels.fieldsChanged",
            "source": "Important: There are unsaved changes in the form. Continuing without saving will discard your changes. Continue?",
            "resource": "EXT:core/Resources/Private/Language/locallang_core.xlf"
        },
        {
            "ref": "core.core:mess.refreshRequired.content",
            "domain": "core.core",
            "key": "mess.refreshRequired.content",
            "source": "This change will affect which fields are available in the form. Would you like to save now in order to refresh the display?",
            "resource": "EXT:core/Resources/Private/Language/locallang_core.xlf"
        },
        {
            "ref": "core.core:mess.refreshRequired.confirm",
            "domain": "core.core",
            "key": "mess.refreshRequired.confirm",
            "source": "Save and refresh",
            "resource": "EXT:core/Resources/Private/Language/locallang_core.xlf"
        },
        {
            "ref": "core.core:rm.saveDoc",
            "domain": "core.core",
            "key": "rm.saveDoc",
            "source": "Save",
            "resource": "EXT:core/Resources/Private/Language/locallang_core.xlf"
        },
        {
            "ref": "core.core:rm.saveDocShow",
            "domain": "core.core",
            "key": "rm.saveDocShow",
            "source": "Save and view page",
            "resource": "EXT:core/Resources/Private/Language/locallang_core.xlf"
        },
        {
            "ref": "core.core:rm.saveCloseDoc",
            "domain": "core.core",
            "key": "rm.saveCloseDoc",
            "source": "Save and close",
            "resource": "EXT:core/Resources/Private/Language/locallang_core.xlf"
        },
        {
            "ref": "core.core:rm.saveCloseAllDocs",
            "domain": "core.core",
            "key": "rm.saveCloseAllDocs",
            "source": "Save and close all",
            "resource": "EXT:core/Resources/Private/Language/locallang_core.xlf"
        },
        {
            "ref": "core.core:rm.saveNewDoc",
            "domain": "core.core",
            "key": "rm.saveNewDoc",
            "source": "Save and create a new one",
            "resource": "EXT:core/Resources/Private/Language/locallang_core.xlf"
        },
        {
            "ref": "core.core:cm.save",
            "domain": "core.core",
            "key": "cm.save",
            "source": "Save",
            "resource": "EXT:core/Resources/Private/Language/locallang_core.xlf"
        },
        {
            "ref": "core.password_policy:dataHandler.passwordNotSaved",
            "domain": "core.password_policy",
            "key": "dataHandler.passwordNotSaved",
            "source": "Password not saved ({table}:{uid}):",
            "resource": "EXT:core/Resources/Private/Language/locallang_password_policy.xlf"
        },
        {
            "ref": "backend.alt_doc:buttons.confirm.duplicate_record_changed.yes",
            "domain": "backend.alt_doc",
            "key": "buttons.confirm.duplicate_record_changed.yes",
            "source": "Yes, save and duplicate this record",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.alt_doc:buttons.confirm.close_without_save.yes",
            "domain": "backend.alt_doc",
            "key": "buttons.confirm.close_without_save.yes",
            "source": "Discard changes",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.alt_doc:buttons.confirm.close_without_save.no",
            "domain": "backend.alt_doc",
            "key": "buttons.confirm.close_without_save.no",
            "source": "Keep editing",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.alt_doc:buttons.confirm.save_and_close",
            "domain": "backend.alt_doc",
            "key": "buttons.confirm.save_and_close",
            "source": "Save and close",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.alt_doc:label.confirm.duplicate_record_changed.title",
            "domain": "backend.alt_doc",
            "key": "label.confirm.duplicate_record_changed.title",
            "source": "Do you want to save before duplicating this record?",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        },
        {
            "ref": "backend.alt_doc:label.confirm.duplicate_record_changed.content",
            "domain": "backend.alt_doc",
            "key": "label.confirm.duplicate_record_changed.content",
            "source": "You currently have unsaved changes. Do you want to save your changes before duplicating this record?",
            "resource": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
        }
    ],
    "terms": [
        {
            "term": "save",
            "matchCount": 101
        }
    ],
    "answeredBy": "installation"
}
```

## labels: miss

Called with:

```json
{
    "query": "quantumflux"
}
```

### From the 14.3 core checkout below .checkouts/, whose console could not be reached

Text:

```
No label in <installation> matches "quantumflux". This is an answer about your installation rather than about TYPO3 in general.

A match is reusable only when its resource is the one already used at the consuming code. A label from another module or package is not a shared vocabulary merely because its text matches; call again with resource once that usage context is known.

Read from the XLF files of the installed packages: the console could not be asked (<installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists). What that leaves out is the assembled runtime state — a label an installation replaces through LANG/resourceOverrides is shown here as its package ships it.
```

Data:

```json
{
    "query": "quantumflux",
    "resource": null,
    "matchCount": 0,
    "labels": [],
    "terms": [
        {
            "term": "quantumflux",
            "matchCount": 0
        }
    ],
    "answeredBy": "packages"
}
```

### From the E-SITE this repository makes below .environments/, whose console answers

Text:

```
No label in <installation> matches "quantumflux". This is an answer about your installation rather than about TYPO3 in general.

A match is reusable only when its resource is the one already used at the consuming code. A label from another module or package is not a shared vocabulary merely because its text matches; call again with resource once that usage context is known.
```

Data:

```json
{
    "query": "quantumflux",
    "resource": null,
    "matchCount": 0,
    "labels": [],
    "terms": [
        {
            "term": "quantumflux",
            "matchCount": 0
        }
    ],
    "answeredBy": "installation"
}
```
