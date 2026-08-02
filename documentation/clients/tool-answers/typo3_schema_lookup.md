# What `typo3_schema_lookup` answered

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

## schema: one table

Called with:

```json
{
    "table": "tt_content"
}
```

### From the 14.3 core checkout below .checkouts/, whose console could not be reached

Text:

```
This is not answerable here, which is not the same as an empty answer: <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists.
typo3_server_scope reports the installation and its console.
```

Data:

```json
{
    "table": "tt_content",
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

### From the E-SITE this repository makes below .environments/, whose console answers

Text:

```
TYPO3 derives 67 columns for tt_content from its TCA. An ext_tables.sql that declares one of them again is declaring what the core already creates.

- uid integer NOT NULL
- pid integer NOT NULL DEFAULT 0
- tstamp integer NOT NULL DEFAULT 0
- crdate integer NOT NULL DEFAULT 0
- deleted smallint NOT NULL DEFAULT 0
- hidden smallint NOT NULL DEFAULT 0
- starttime integer NOT NULL DEFAULT 0
- endtime integer NOT NULL DEFAULT 0
- fe_group string NOT NULL DEFAULT '0'
- sorting integer NOT NULL DEFAULT 0
- rowDescription text
- editlock smallint NOT NULL DEFAULT 0
- sys_language_uid integer NOT NULL DEFAULT 0
- l18n_parent integer NOT NULL DEFAULT 0
- l10n_source integer NOT NULL DEFAULT 0
- l10n_state text
- l18n_diffsource blob
- t3ver_oid integer NOT NULL DEFAULT 0
- t3ver_wsid integer NOT NULL DEFAULT 0
- t3ver_state smallint NOT NULL DEFAULT 0
- t3ver_stage integer NOT NULL DEFAULT 0
- CType string NOT NULL DEFAULT 'text'
- categories integer NOT NULL DEFAULT 0
- layout integer NOT NULL DEFAULT 0
- frame_class string NOT NULL DEFAULT 'default'
- space_before_class string NOT NULL DEFAULT ''
- space_after_class string NOT NULL DEFAULT ''
- colPos text
- date bigint NOT NULL DEFAULT 0
- header string NOT NULL DEFAULT ''
- header_layout integer NOT NULL DEFAULT 0
- header_position string NOT NULL DEFAULT ''
- header_link text NOT NULL DEFAULT ''
- subheader string NOT NULL DEFAULT ''
- bodytext text
- image integer NOT NULL DEFAULT 0
- assets integer NOT NULL DEFAULT 0
- imagewidth integer
- imageheight integer
- imageorient integer NOT NULL DEFAULT 0
- imageborder smallint NOT NULL DEFAULT 0
- image_zoom smallint NOT NULL DEFAULT 0
- imagecols integer NOT NULL DEFAULT 2
- pages text
- recursive integer NOT NULL DEFAULT 0
- media integer NOT NULL DEFAULT 0
- records text
- sectionIndex smallint NOT NULL DEFAULT 1
- linkToTop smallint NOT NULL DEFAULT 0
- pi_flexform text
- selected_categories text
- category_field string NOT NULL DEFAULT ''
- bullets_type integer NOT NULL DEFAULT 0
- cols integer NOT NULL DEFAULT 0
- table_class string NOT NULL DEFAULT ''
- table_caption string NOT NULL DEFAULT ''
- table_delimiter integer NOT NULL DEFAULT 124
- table_enclosure integer NOT NULL DEFAULT 0
- table_header_position integer NOT NULL DEFAULT 0
- table_tfoot smallint NOT NULL DEFAULT 0
- file_collections text
- filelink_size smallint NOT NULL DEFAULT 0
- filelink_sorting string NOT NULL DEFAULT ''
- filelink_sorting_direction string NOT NULL DEFAULT ''
- target string NOT NULL DEFAULT ''
- uploads_description smallint NOT NULL DEFAULT 0
- uploads_type integer NOT NULL DEFAULT 0
```

Data:

```json
{
    "table": "tt_content",
    "matchCount": 67,
    "answeredBy": "installation",
    "columns": [
        {
            "name": "uid",
            "type": "integer",
            "notnull": true,
            "default": null,
            "length": null
        },
        {
            "name": "pid",
            "type": "integer",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "tstamp",
            "type": "integer",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "crdate",
            "type": "integer",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "deleted",
            "type": "smallint",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "hidden",
            "type": "smallint",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "starttime",
            "type": "integer",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "endtime",
            "type": "integer",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "fe_group",
            "type": "string",
            "notnull": true,
            "default": "0",
            "length": 255
        },
        {
            "name": "sorting",
            "type": "integer",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "rowDescription",
            "type": "text",
            "notnull": false,
            "default": null,
            "length": 65535
        },
        {
            "name": "editlock",
            "type": "smallint",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "sys_language_uid",
            "type": "integer",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "l18n_parent",
            "type": "integer",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "l10n_source",
            "type": "integer",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "l10n_state",
            "type": "text",
            "notnull": false,
            "default": null,
            "length": 65535
        },
        {
            "name": "l18n_diffsource",
            "type": "blob",
            "notnull": false,
            "default": null,
            "length": 16777215
        },
        {
            "name": "t3ver_oid",
            "type": "integer",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "t3ver_wsid",
            "type": "integer",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "t3ver_state",
            "type": "smallint",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "t3ver_stage",
            "type": "integer",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "CType",
            "type": "string",
            "notnull": true,
            "default": "text",
            "length": 255
        },
        {
            "name": "categories",
            "type": "integer",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "layout",
            "type": "integer",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "frame_class",
            "type": "string",
            "notnull": true,
            "default": "default",
            "length": 255
        },
        {
            "name": "space_before_class",
            "type": "string",
            "notnull": true,
            "default": "",
            "length": 60
        },
        {
            "name": "space_after_class",
            "type": "string",
            "notnull": true,
            "default": "",
            "length": 60
        },
        {
            "name": "colPos",
            "type": "text",
            "notnull": false,
            "default": null,
            "length": null
        },
        {
            "name": "date",
            "type": "bigint",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "header",
            "type": "string",
            "notnull": true,
            "default": "",
            "length": 255
        },
        {
            "name": "header_layout",
            "type": "integer",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "header_position",
            "type": "string",
            "notnull": true,
            "default": "",
            "length": 255
        },
        {
            "name": "header_link",
            "type": "text",
            "notnull": true,
            "default": "",
            "length": 65535
        },
        {
            "name": "subheader",
            "type": "string",
            "notnull": true,
            "default": "",
            "length": 255
        },
        {
            "name": "bodytext",
            "type": "text",
            "notnull": false,
            "default": null,
            "length": null
        },
        {
            "name": "image",
            "type": "integer",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "assets",
            "type": "integer",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "imagewidth",
            "type": "integer",
            "notnull": false,
            "default": null,
            "length": null
        },
        {
            "name": "imageheight",
            "type": "integer",
            "notnull": false,
            "default": null,
            "length": null
        },
        {
            "name": "imageorient",
            "type": "integer",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "imageborder",
            "type": "smallint",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "image_zoom",
            "type": "smallint",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "imagecols",
            "type": "integer",
            "notnull": true,
            "default": 2,
            "length": null
        },
        {
            "name": "pages",
            "type": "text",
            "notnull": false,
            "default": null,
            "length": null
        },
        {
            "name": "recursive",
            "type": "integer",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "media",
            "type": "integer",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "records",
            "type": "text",
            "notnull": false,
            "default": null,
            "length": null
        },
        {
            "name": "sectionIndex",
            "type": "smallint",
            "notnull": true,
            "default": 1,
            "length": null
        },
        {
            "name": "linkToTop",
            "type": "smallint",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "pi_flexform",
            "type": "text",
            "notnull": false,
            "default": null,
            "length": null
        },
        {
            "name": "selected_categories",
            "type": "text",
            "notnull": false,
            "default": null,
            "length": null
        },
        {
            "name": "category_field",
            "type": "string",
            "notnull": true,
            "default": "",
            "length": 64
        },
        {
            "name": "bullets_type",
            "type": "integer",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "cols",
            "type": "integer",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "table_class",
            "type": "string",
            "notnull": true,
            "default": "",
            "length": 60
        },
        {
            "name": "table_caption",
            "type": "string",
            "notnull": true,
            "default": "",
            "length": 255
        },
        {
            "name": "table_delimiter",
            "type": "integer",
            "notnull": true,
            "default": 124,
            "length": null
        },
        {
            "name": "table_enclosure",
            "type": "integer",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "table_header_position",
            "type": "integer",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "table_tfoot",
            "type": "smallint",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "file_collections",
            "type": "text",
            "notnull": false,
            "default": null,
            "length": null
        },
        {
            "name": "filelink_size",
            "type": "smallint",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "filelink_sorting",
            "type": "string",
            "notnull": true,
            "default": "",
            "length": 64
        },
        {
            "name": "filelink_sorting_direction",
            "type": "string",
            "notnull": true,
            "default": "",
            "length": 4
        },
        {
            "name": "target",
            "type": "string",
            "notnull": true,
            "default": "",
            "length": 30
        },
        {
            "name": "uploads_description",
            "type": "smallint",
            "notnull": true,
            "default": 0,
            "length": null
        },
        {
            "name": "uploads_type",
            "type": "integer",
            "notnull": true,
            "default": 0,
            "length": null
        }
    ],
    "tables": []
}
```

## schema: every table

Called with:

```json
{}
```

### From the 14.3 core checkout below .checkouts/, whose console could not be reached

Text:

```
This is not answerable here, which is not the same as an empty answer: <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists.
typo3_server_scope reports the installation and its console.
```

Data:

```json
{
    "table": null,
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

### From the E-SITE this repository makes below .environments/, whose console answers

Text:

```
TYPO3 derives columns for 23 tables in this installation.
Name one to see its columns. What is listed for it is what an ext_tables.sql may leave out.

- be_groups: 25 columns
- be_users: 29 columns
- pages: 69 columns
- sys_category: 21 columns
- sys_file: 12 columns
- sys_file_collection: 23 columns
- sys_file_metadata: 19 columns
- sys_file_reference: 25 columns
- sys_file_storage: 15 columns
- sys_filemounts: 10 columns
- sys_news: 10 columns
- backend_layout: 15 columns
- fe_groups: 10 columns
- fe_users: 30 columns
- sys_template: 19 columns
- tt_content: 67 columns
- be_dashboards: 10 columns
- tx_impexp_presets: 4 columns
- form_definition: 8 columns
- sys_reaction: 17 columns
- sys_note: 11 columns
- sys_webhook: 17 columns
- sys_category_record_mm: 6 columns (created for an MM relation; declare nothing for it)
```

Data:

```json
{
    "table": null,
    "matchCount": 23,
    "answeredBy": "installation",
    "columns": [],
    "tables": [
        {
            "table": "be_groups",
            "columnCount": 25,
            "relationTable": false
        },
        {
            "table": "be_users",
            "columnCount": 29,
            "relationTable": false
        },
        {
            "table": "pages",
            "columnCount": 69,
            "relationTable": false
        },
        {
            "table": "sys_category",
            "columnCount": 21,
            "relationTable": false
        },
        {
            "table": "sys_file",
            "columnCount": 12,
            "relationTable": false
        },
        {
            "table": "sys_file_collection",
            "columnCount": 23,
            "relationTable": false
        },
        {
            "table": "sys_file_metadata",
            "columnCount": 19,
            "relationTable": false
        },
        {
            "table": "sys_file_reference",
            "columnCount": 25,
            "relationTable": false
        },
        {
            "table": "sys_file_storage",
            "columnCount": 15,
            "relationTable": false
        },
        {
            "table": "sys_filemounts",
            "columnCount": 10,
            "relationTable": false
        },
        {
            "table": "sys_news",
            "columnCount": 10,
            "relationTable": false
        },
        {
            "table": "backend_layout",
            "columnCount": 15,
            "relationTable": false
        },
        {
            "table": "fe_groups",
            "columnCount": 10,
            "relationTable": false
        },
        {
            "table": "fe_users",
            "columnCount": 30,
            "relationTable": false
        },
        {
            "table": "sys_template",
            "columnCount": 19,
            "relationTable": false
        },
        {
            "table": "tt_content",
            "columnCount": 67,
            "relationTable": false
        },
        {
            "table": "be_dashboards",
            "columnCount": 10,
            "relationTable": false
        },
        {
            "table": "tx_impexp_presets",
            "columnCount": 4,
            "relationTable": false
        },
        {
            "table": "form_definition",
            "columnCount": 8,
            "relationTable": false
        },
        {
            "table": "sys_reaction",
            "columnCount": 17,
            "relationTable": false
        },
        {
            "table": "sys_note",
            "columnCount": 11,
            "relationTable": false
        },
        {
            "table": "sys_webhook",
            "columnCount": 17,
            "relationTable": false
        },
        {
            "table": "sys_category_record_mm",
            "columnCount": 6,
            "relationTable": true
        }
    ]
}
```
