# `typo3_backend_module_lookup`

List the backend modules registered in the TYPO3 installation you are working
in, with the extension that declares each one, its place in the module tree, its
labels and its route. Answered by the installation, so a project extension's
modules are in it.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

## Takes

```yaml
# Module identifier, label, route, or extension name to filter by. Omit to list
# every module.
query: string  # optional
```

## Answers with

```yaml
query: string
matchCount: integer  # optional
# One of: installation, packages. installation: its assembled runtime state
# answered. packages: read from the files the installed packages ship, because
# the console could not be asked — overrides applied at runtime are not
# reflected.
answeredBy: string  # optional
modules:  # optional
  - identifier: string
    # The modules it sits under, outermost first.
    parents: [string]
    # The package that declares it.
    extension: string
    # Its label, with the translation domain reference behind it.
    labels: string  # optional
    # The backend route it answers on.
    path: string
    # Its declared before/after position, if any.
    position: string  # optional
unsupported:  # optional
  # One of: no-installation, misconfigured, installation-not-answering.
  # no-installation: nothing to ask from here, and searched says where it
  # looked. misconfigured: an installation was named and could not be used, so
  # nothing was searched for. installation-not-answering: one was found and its
  # console did not answer — a stopped container or a database with no schema,
  # which is a state that ends without reinstalling anything.
  cause: string
  # What stopped it, in the words the attempt produced.
  reason: string
  # What the reason means where the message alone does not say it — a console
  # that starts and then fails on a missing table has a database without a
  # schema, not a broken installation. Empty where nothing beyond the reason is
  # known.
  diagnosis: string  # optional
  # Every directory the discovery walked, in order. "Nothing was found" and "the
  # server was started somewhere else" wear one sentence, and only this tells
  # them apart. Empty where discovery never ran.
  searched: [string]
  # What was set and could not be used. Null where nothing was set.
  misconfiguration: string or null  # optional
  settings:
    # Environment variable that names the installation root.
    root: string
    # Environment variable that names the console command.
    console: string
```

The answer carries exactly one of these sets of fields: `query`, `matchCount`,
`answeredBy`, `modules` — or `query`, `unsupported`.

## Answered

Recorded on 2026-08-02 by `bin/cli tools:record`. Of two working directories,
because what this server answers depends on which one a client is standing in,
and neither fills the whole surface. Answered against core-checkout, TYPO3
14.3.6-dev, the 14.3 core checkout below .checkouts/, whose console could not be
reached: <installation> has no TYPO3 console — none of bin/typo3,
vendor/bin/typo3 exists. Answered against composer-project, TYPO3 14.3.5, the
E-SITE this repository makes below .environments/, whose console answers. The
tools that declare `answeredBy` carry an answer from each, under a heading
naming which; every other answer is from the first alone, because nothing in it
would differ. Nothing checks what is below this heading; everything above it is
derived from the class that answers the call, and `bin/cli tools:check` holds
it.

### modules

Called with:

```json
{}
```

#### From the 14.3 core checkout below .checkouts/, whose console could not be reached

Text:

```
This is not answerable here, which is not the same as an empty answer: <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists.
typo3_server_scope reports the installation and its console.
```

Data:

```json
{
    "query": "",
    "unsupported": {
        "cause": "installation-not-answering",
        "reason": "<installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists",
        "diagnosis": "",
        "searched": [
            "<installation>"
        ],
        "misconfiguration": null,
        "settings": {
            "root": "TYPO3_MCP_ROOT",
            "console": "TYPO3_MCP_CONSOLE"
        }
    }
}
```

#### From the E-SITE this repository makes below .environments/, whose console answers

Text:

```
47 backend module(s):
- dashboard
  /module/dashboard  (typo3/cms-dashboard)
  Dashboard [dashboard.module]
- content
  /module/content  (typo3/cms-core)
  Content [core.modules.content]
- content > web_layout
  /module/web/layout  (typo3/cms-backend)
  Layout [backend.modules.layout]
- content > records
  /module/content/records  (typo3/cms-backend)
  Records [backend.modules.list]
- content > page_preview
  /module/page-preview  (typo3/cms-viewpage)
  Preview [viewpage.module]
- content > content_status
  /module/content/status  (typo3/cms-backend)
  Status [backend.modules.status]
- content > content_status > web_info_overview
  /module/web/info/overview  (typo3/cms-info)
  Pagetree Overview [info.modules.overview]
- content > content_status > web_info_translations
  /module/web/info/translations  (typo3/cms-info)
  Localization Overview [info.modules.translations]
- content > recycler
  /module/web/recycler  (typo3/cms-recycler)
  Recycler [recycler.module]
- content > web_FormFormbuilder
  /module/form  (typo3/cms-form)
  Forms [form.module]
- content > web_FormFormbuilder > form_manager
  /module/form/overview  (typo3/cms-form)
  Form Manager [form.modules.form_manager]
- content > web_FormFormbuilder > form_editor
  /module/form/editor  (typo3/cms-form)
  Form Editor [form.modules.form_editor]
- media_management
  /module/file/list  (typo3/cms-filelist)
  Media [filelist.module]
- site
  /module/site  (typo3/cms-core)
  Sites [core.modules.site]
- site > site_configuration
  /module/site/configuration  (typo3/cms-backend)
  Setup [backend.modules.site_configuration]
- site > link_management
  /module/link-management  (typo3/cms-backend)
  Link Management [backend.modules.link_management]
- site > pagetsconfig
  /module/pagetsconfig  (typo3/cms-backend)
  Page TSconfig [backend.modules.pagetsconfig]
- site > pagetsconfig > pagetsconfig_pages
  /module/pagetsconfig/records  (typo3/cms-backend)
  Pages containing page TSconfig [backend.modules.pagetsconfig_pages]
- site > pagetsconfig > pagetsconfig_active
  /module/pagetsconfig/active  (typo3/cms-backend)
  Active page TSconfig [backend.modules.pagetsconfig_active]
- site > pagetsconfig > pagetsconfig_includes
  /module/pagetsconfig/includes  (typo3/cms-backend)
  Included page TSconfig [backend.modules.pagetsconfig_includes]
- site > web_ts
  /module/web/ts  (typo3/cms-tstemplate)
  TypoScript [tstemplate.modules.ts]
- site > web_ts > web_typoscript_recordsoverview
  /module/web/typoscript/records-overview  (typo3/cms-tstemplate)
  TypoScript Overview [tstemplate.modules.recordsoverview]
- site > web_ts > web_typoscript_constanteditor
  /module/web/typoscript/constant-editor  (typo3/cms-tstemplate)
  Constant Editor [tstemplate.modules.constanteditor]
- site > web_ts > web_typoscript_infomodify
  /module/web/typoscript/overview  (typo3/cms-tstemplate)
  Edit TypoScript Record [tstemplate.modules.infomodify]
- site > web_ts > typoscript_active
  /module/typoscript/active  (typo3/cms-tstemplate)
  Active TypoScript [tstemplate.modules.active]
- site > web_ts > web_typoscript_analyzer
  /module/web/typoscript/analyzer  (typo3/cms-tstemplate)
  Included TypoScript [tstemplate.modules.analyzer]
- user
  /module/user  (typo3/cms-core)
  User Tools [core.modules.user]
- user > user_setup
  /module/user/setup  (typo3/cms-backend)
  User Settings [backend.modules.user_settings]
- admin
  /module/admin  (typo3/cms-core)
  Administration [core.modules.admin]
- admin > backend_user_management
  /module/users/management  (typo3/cms-beuser)
  Users [beuser.modules.user_management]
- admin > permissions_pages
  /module/users/permissions  (typo3/cms-beuser)
  Permissions [beuser.modules.permissions]
- admin > integrations
  /module/integrations  (typo3/cms-core)
  Integrations [core.modules.integrations]
- admin > integrations > integrations_reactions
  /module/integrations/reactions  (typo3/cms-reactions)
  Reactions [reactions.module]
- admin > integrations > integrations_webhooks
  /module/integrations/webhooks  (typo3/cms-webhooks)
  Webhooks [webhooks.module]
- admin > system_log
  /module/system/log  (typo3/cms-belog)
  Log [belog.module]
- system
  /module/system  (typo3/cms-core)
  System [core.modules.system]
- system > system_maintenance
  /module/system/maintenance  (typo3/cms-install)
  Maintenance [install.modules.maintenance]
- system > system_settings
  /module/system/settings  (typo3/cms-install)
  Settings [install.modules.settings]
- system > system_upgrade
  /module/system/upgrade  (typo3/cms-install)
  Upgrade [install.modules.upgrade]
- system > system_environment
  /module/system/environment  (typo3/cms-install)
  Environment [install.modules.environment]
- system > system_database
  /module/system/database  (typo3/cms-lowlevel)
  Database [lowlevel.modules.database_integrity]
- system > system_database > system_database_raw
  /module/system/database/raw  (typo3/cms-lowlevel)
  Raw search [lowlevel.modules.database_raw]
- system > system_database > system_database_query
  /module/system/database/query  (typo3/cms-lowlevel)
  Advanced query [lowlevel.modules.database_query]
- system > system_config
  /module/system/config  (typo3/cms-lowlevel)
  Configuration [lowlevel.modules.config]
- system > content_security_policy
  /module/content/security/policy  (typo3/cms-backend)
  Content Security Policy [backend.modules.content_security_policy]
- help
  /module/help  (typo3/cms-core)
  Help [core.modules.help]
- help > about
  /module/help/about  (typo3/cms-backend)
  About TYPO3 CMS [backend.modules.about]

A module is declared in its extension's Configuration/Backend/Modules.php; the label in brackets is a translation domain reference.
```

Data:

```json
{
    "query": "",
    "matchCount": 47,
    "modules": [
        {
            "identifier": "dashboard",
            "parents": [],
            "extension": "typo3/cms-dashboard",
            "labels": "Dashboard [dashboard.module]",
            "path": "/module/dashboard",
            "position": "{\"before\":\"*\"}"
        },
        {
            "identifier": "content",
            "parents": [],
            "extension": "typo3/cms-core",
            "labels": "Content [core.modules.content]",
            "path": "/module/content",
            "position": ""
        },
        {
            "identifier": "web_layout",
            "parents": [
                "content"
            ],
            "extension": "typo3/cms-backend",
            "labels": "Layout [backend.modules.layout]",
            "path": "/module/web/layout",
            "position": "{\"before\":\"*\"}"
        },
        {
            "identifier": "records",
            "parents": [
                "content"
            ],
            "extension": "typo3/cms-backend",
            "labels": "Records [backend.modules.list]",
            "path": "/module/content/records",
            "position": "{\"after\":\"web_layout\"}"
        },
        {
            "identifier": "page_preview",
            "parents": [
                "content"
            ],
            "extension": "typo3/cms-viewpage",
            "labels": "Preview [viewpage.module]",
            "path": "/module/page-preview",
            "position": "{\"after\":\"records\"}"
        },
        {
            "identifier": "content_status",
            "parents": [
                "content"
            ],
            "extension": "typo3/cms-backend",
            "labels": "Status [backend.modules.status]",
            "path": "/module/content/status",
            "position": "{\"after\":\"web_FormFormbuilder\",\"before\":\"recycler\"}"
        },
        {
            "identifier": "web_info_overview",
            "parents": [
                "content",
                "content_status"
            ],
            "extension": "typo3/cms-info",
            "labels": "Pagetree Overview [info.modules.overview]",
            "path": "/module/web/info/overview",
            "position": "{\"before\":\"*\"}"
        },
        {
            "identifier": "web_info_translations",
            "parents": [
                "content",
                "content_status"
            ],
            "extension": "typo3/cms-info",
            "labels": "Localization Overview [info.modules.translations]",
            "path": "/module/web/info/translations",
            "position": "{\"after\":\"web_info_overview\"}"
        },
        {
            "identifier": "recycler",
            "parents": [
                "content"
            ],
            "extension": "typo3/cms-recycler",
            "labels": "Recycler [recycler.module]",
            "path": "/module/web/recycler",
            "position": "{\"after\":\"content_status\"}"
        },
        {
            "identifier": "web_FormFormbuilder",
            "parents": [
                "content"
            ],
            "extension": "typo3/cms-form",
            "labels": "Forms [form.module]",
            "path": "/module/form",
            "position": "{\"after\":\"workspaces_admin\"}"
        },
        {
            "identifier": "form_manager",
            "parents": [
                "content",
                "web_FormFormbuilder"
            ],
            "extension": "typo3/cms-form",
            "labels": "Form Manager [form.modules.form_manager]",
            "path": "/module/form/overview",
            "position": ""
        },
        {
            "identifier": "form_editor",
            "parents": [
                "content",
                "web_FormFormbuilder"
            ],
            "extension": "typo3/cms-form",
            "labels": "Form Editor [form.modules.form_editor]",
            "path": "/module/form/editor",
            "position": ""
        },
        {
            "identifier": "media_management",
            "parents": [],
            "extension": "typo3/cms-filelist",
            "labels": "Media [filelist.module]",
            "path": "/module/file/list",
            "position": "{\"after\":\"content\"}"
        },
        {
            "identifier": "site",
            "parents": [],
            "extension": "typo3/cms-core",
            "labels": "Sites [core.modules.site]",
            "path": "/module/site",
            "position": ""
        },
        {
            "identifier": "site_configuration",
            "parents": [
                "site"
            ],
            "extension": "typo3/cms-backend",
            "labels": "Setup [backend.modules.site_configuration]",
            "path": "/module/site/configuration",
            "position": "{\"before\":\"*\"}"
        },
        {
            "identifier": "link_management",
            "parents": [
                "site"
            ],
            "extension": "typo3/cms-backend",
            "labels": "Link Management [backend.modules.link_management]",
            "path": "/module/link-management",
            "position": "{\"after\":\"site_configuration\"}"
        },
        {
            "identifier": "pagetsconfig",
            "parents": [
                "site"
            ],
            "extension": "typo3/cms-backend",
            "labels": "Page TSconfig [backend.modules.pagetsconfig]",
            "path": "/module/pagetsconfig",
            "position": ""
        },
        {
            "identifier": "pagetsconfig_pages",
            "parents": [
                "site",
                "pagetsconfig"
            ],
            "extension": "typo3/cms-backend",
            "labels": "Pages containing page TSconfig [backend.modules.pagetsconfig_pages]",
            "path": "/module/pagetsconfig/records",
            "position": ""
        },
        {
            "identifier": "pagetsconfig_active",
            "parents": [
                "site",
                "pagetsconfig"
            ],
            "extension": "typo3/cms-backend",
            "labels": "Active page TSconfig [backend.modules.pagetsconfig_active]",
            "path": "/module/pagetsconfig/active",
            "position": ""
        },
        {
            "identifier": "pagetsconfig_includes",
            "parents": [
                "site",
                "pagetsconfig"
            ],
            "extension": "typo3/cms-backend",
            "labels": "Included page TSconfig [backend.modules.pagetsconfig_includes]",
            "path": "/module/pagetsconfig/includes",
            "position": ""
        },
        {
            "identifier": "web_ts",
            "parents": [
                "site"
            ],
            "extension": "typo3/cms-tstemplate",
            "labels": "TypoScript [tstemplate.modules.ts]",
            "path": "/module/web/ts",
            "position": ""
        },
        {
            "identifier": "web_typoscript_recordsoverview",
            "parents": [
                "site",
                "web_ts"
            ],
            "extension": "typo3/cms-tstemplate",
            "labels": "TypoScript Overview [tstemplate.modules.recordsoverview]",
            "path": "/module/web/typoscript/records-overview",
            "position": ""
        },
        {
            "identifier": "web_typoscript_constanteditor",
            "parents": [
                "site",
                "web_ts"
            ],
            "extension": "typo3/cms-tstemplate",
            "labels": "Constant Editor [tstemplate.modules.constanteditor]",
            "path": "/module/web/typoscript/constant-editor",
            "position": ""
        },
        {
            "identifier": "web_typoscript_infomodify",
            "parents": [
                "site",
                "web_ts"
            ],
            "extension": "typo3/cms-tstemplate",
            "labels": "Edit TypoScript Record [tstemplate.modules.infomodify]",
            "path": "/module/web/typoscript/overview",
            "position": ""
        },
        {
            "identifier": "typoscript_active",
            "parents": [
                "site",
                "web_ts"
            ],
            "extension": "typo3/cms-tstemplate",
            "labels": "Active TypoScript [tstemplate.modules.active]",
            "path": "/module/typoscript/active",
            "position": ""
        },
        {
            "identifier": "web_typoscript_analyzer",
            "parents": [
                "site",
                "web_ts"
            ],
            "extension": "typo3/cms-tstemplate",
            "labels": "Included TypoScript [tstemplate.modules.analyzer]",
            "path": "/module/web/typoscript/analyzer",
            "position": ""
        },
        {
            "identifier": "user",
            "parents": [],
            "extension": "typo3/cms-core",
            "labels": "User Tools [core.modules.user]",
            "path": "/module/user",
            "position": ""
        },
        {
            "identifier": "user_setup",
            "parents": [
                "user"
            ],
            "extension": "typo3/cms-backend",
            "labels": "User Settings [backend.modules.user_settings]",
            "path": "/module/user/setup",
            "position": ""
        },
        {
            "identifier": "admin",
            "parents": [],
            "extension": "typo3/cms-core",
            "labels": "Administration [core.modules.admin]",
            "path": "/module/admin",
            "position": ""
        },
        {
            "identifier": "backend_user_management",
            "parents": [
                "admin"
            ],
            "extension": "typo3/cms-beuser",
            "labels": "Users [beuser.modules.user_management]",
            "path": "/module/users/management",
            "position": "{\"before\":\"*\"}"
        },
        {
            "identifier": "permissions_pages",
            "parents": [
                "admin"
            ],
            "extension": "typo3/cms-beuser",
            "labels": "Permissions [beuser.modules.permissions]",
            "path": "/module/users/permissions",
            "position": "{\"after\":\"scheduler\"}"
        },
        {
            "identifier": "integrations",
            "parents": [
                "admin"
            ],
            "extension": "typo3/cms-core",
            "labels": "Integrations [core.modules.integrations]",
            "path": "/module/integrations",
            "position": "{\"after\":\"permissions_pages\"}"
        },
        {
            "identifier": "integrations_reactions",
            "parents": [
                "admin",
                "integrations"
            ],
            "extension": "typo3/cms-reactions",
            "labels": "Reactions [reactions.module]",
            "path": "/module/integrations/reactions",
            "position": ""
        },
        {
            "identifier": "integrations_webhooks",
            "parents": [
                "admin",
                "integrations"
            ],
            "extension": "typo3/cms-webhooks",
            "labels": "Webhooks [webhooks.module]",
            "path": "/module/integrations/webhooks",
            "position": ""
        },
        {
            "identifier": "system_log",
            "parents": [
                "admin"
            ],
            "extension": "typo3/cms-belog",
            "labels": "Log [belog.module]",
            "path": "/module/system/log",
            "position": "{\"after\":\"integrations\"}"
        },
        {
            "identifier": "system",
            "parents": [],
            "extension": "typo3/cms-core",
            "labels": "System [core.modules.system]",
            "path": "/module/system",
            "position": ""
        },
        {
            "identifier": "system_maintenance",
            "parents": [
                "system"
            ],
            "extension": "typo3/cms-install",
            "labels": "Maintenance [install.modules.maintenance]",
            "path": "/module/system/maintenance",
            "position": "{\"before\":\"*\"}"
        },
        {
            "identifier": "system_settings",
            "parents": [
                "system"
            ],
            "extension": "typo3/cms-install",
            "labels": "Settings [install.modules.settings]",
            "path": "/module/system/settings",
            "position": "{\"before\":\"*\"}"
        },
        {
            "identifier": "system_upgrade",
            "parents": [
                "system"
            ],
            "extension": "typo3/cms-install",
            "labels": "Upgrade [install.modules.upgrade]",
            "path": "/module/system/upgrade",
            "position": "{\"before\":\"*\"}"
        },
        {
            "identifier": "system_environment",
            "parents": [
                "system"
            ],
            "extension": "typo3/cms-install",
            "labels": "Environment [install.modules.environment]",
            "path": "/module/system/environment",
            "position": "{\"before\":\"*\"}"
        },
        {
            "identifier": "system_database",
            "parents": [
                "system"
            ],
            "extension": "typo3/cms-lowlevel",
            "labels": "Database [lowlevel.modules.database_integrity]",
            "path": "/module/system/database",
            "position": "{\"after\":\"*\"}"
        },
        {
            "identifier": "system_database_raw",
            "parents": [
                "system",
                "system_database"
            ],
            "extension": "typo3/cms-lowlevel",
            "labels": "Raw search [lowlevel.modules.database_raw]",
            "path": "/module/system/database/raw",
            "position": ""
        },
        {
            "identifier": "system_database_query",
            "parents": [
                "system",
                "system_database"
            ],
            "extension": "typo3/cms-lowlevel",
            "labels": "Advanced query [lowlevel.modules.database_query]",
            "path": "/module/system/database/query",
            "position": ""
        },
        {
            "identifier": "system_config",
            "parents": [
                "system"
            ],
            "extension": "typo3/cms-lowlevel",
            "labels": "Configuration [lowlevel.modules.config]",
            "path": "/module/system/config",
            "position": "{\"after\":\"*\"}"
        },
        {
            "identifier": "content_security_policy",
            "parents": [
                "system"
            ],
            "extension": "typo3/cms-backend",
            "labels": "Content Security Policy [backend.modules.content_security_policy]",
            "path": "/module/content/security/policy",
            "position": ""
        },
        {
            "identifier": "help",
            "parents": [],
            "extension": "typo3/cms-core",
            "labels": "Help [core.modules.help]",
            "path": "/module/help",
            "position": ""
        },
        {
            "identifier": "about",
            "parents": [
                "help"
            ],
            "extension": "typo3/cms-backend",
            "labels": "About TYPO3 CMS [backend.modules.about]",
            "path": "/module/help/about",
            "position": "{\"before\":\"*\"}"
        }
    ],
    "answeredBy": "installation"
}
```
