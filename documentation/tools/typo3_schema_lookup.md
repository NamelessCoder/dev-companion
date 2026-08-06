# `typo3_schema_lookup`

List the columns TYPO3 derives for a table from its TCA — uid, pid, the
timestamps, the delete and disable fields, the language and versioning columns,
and one column per TCA field. Those are exactly the columns an ext_tables.sql
does not have to declare, so this is what a redundant declaration is checked
against. The core is asked for them by booting the installation; it says so
rather than answering empty when it cannot. It describes what TYPO3 would
create, never what the database currently has. Answers from: installation.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

Answers from [`installation`](answer-sources.md#installation).

## Takes

```yaml
# The table to list the derived columns of, for example "tt_content". Omit to
# list every table TYPO3 derives columns for, with how many each gets.
table: string  # optional
```

## Answers with

```yaml
# The table asked about. Null where none was named and the answer is the list of
# them.
table: string or null
# Columns for a named table, tables for a call that named none. Zero means the
# name is not a TCA table in this installation, never that TYPO3 derives
# nothing.
matchCount: integer  # optional
# One of: installation. installation: its assembled runtime state answered.
answeredBy: string  # optional
# Empty where no table was named.
columns:  # optional
  - name: string
    # The Doctrine type the core declares it as: integer, string, text,
    # datetime, json, blob.
    type: string
    notnull: boolean
    # The default the core gives it, null where it declares none.
    default: object  # optional
    # Length where the type carries one.
    length: integer or null  # optional
# Every table TYPO3 derives columns for. Returned on a call that named none, and
# on one whose name is not among them.
tables:  # optional
  - table: string
    columnCount: integer
    # True where TYPO3 creates the table itself for an MM relation. No
    # ext_tables.sql declares one at all.
    relationTable: boolean
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

The answer carries exactly one of these sets of fields: `table`, `matchCount`,
`answeredBy`, `columns`, `tables` — or `table`, `unsupported`.

## Answered

Recorded on 2026-08-03 by `bin/cli tools:record`. Of two working directories,
because what this server answers depends on which one a client is standing in,
and neither fills the whole surface. Answered against core-checkout, TYPO3
14.3.6-dev, the 14.3 core checkout below .checkouts/, whose console could not
be reached: <installation> has no TYPO3 console — none of bin/typo3,
vendor/bin/typo3 exists. Answered against composer-project, TYPO3 14.3.0, the
installation this repository writes below .fixtures/, whose console answers.
The tools that declare `answeredBy` carry an answer from each, under a heading
naming which; every other answer is from the first alone, because nothing in it
would differ. Nothing checks what is below this heading; everything above it is
derived from the class that answers the call, and `bin/cli tools:check` holds
it.

### schema: one table

Called with:

```json
{
    "table": "tt_content"
}
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
            "root": "TYPO3_DEV_COMPANION_ROOT",
            "console": "TYPO3_DEV_COMPANION_CONSOLE"
        }
    }
}
```

#### From the installation this repository writes below .fixtures/, whose console answers

Text:

```
TYPO3 derives 4 columns for tt_content from its TCA. An ext_tables.sql that declares one of them again is declaring what the core already creates.

- uid integer NOT NULL
- pid integer NOT NULL DEFAULT 0
- tstamp integer NOT NULL DEFAULT 0
- deleted smallint NOT NULL DEFAULT 0
```

Data:

```json
{
    "table": "tt_content",
    "matchCount": 4,
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
            "name": "deleted",
            "type": "smallint",
            "notnull": true,
            "default": 0,
            "length": null
        }
    ],
    "tables": []
}
```

### schema: every table

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
            "root": "TYPO3_DEV_COMPANION_ROOT",
            "console": "TYPO3_DEV_COMPANION_CONSOLE"
        }
    }
}
```

#### From the installation this repository writes below .fixtures/, whose console answers

Text:

```
TYPO3 derives columns for 4 tables in this installation.
Name one to see its columns. What is listed for it is what an ext_tables.sql may leave out.

- tt_content: 4 columns
- pages: 4 columns
- tx_acme_events_event: 4 columns
- tx_acme_events_event_category_mm: 3 columns (created for an MM relation; declare nothing for it)
```

Data:

```json
{
    "table": null,
    "matchCount": 4,
    "answeredBy": "installation",
    "columns": [],
    "tables": [
        {
            "table": "tt_content",
            "columnCount": 4,
            "relationTable": false
        },
        {
            "table": "pages",
            "columnCount": 4,
            "relationTable": false
        },
        {
            "table": "tx_acme_events_event",
            "columnCount": 4,
            "relationTable": false
        },
        {
            "table": "tx_acme_events_event_category_mm",
            "columnCount": 3,
            "relationTable": true
        }
    ]
}
```
