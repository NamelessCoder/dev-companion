# TYPO3 CMS MCP

A local MCP server (plain PHP) that exposes a curated TYPO3 core contribution
**knowledge base**: contribution rules, core script and `runTests.sh` notes,
architecture hints, commit message conventions, and a catalog of backend UI
components, icon identifiers, and registered labels. It is read-only knowledge —
it does not inspect, read, or run anything against a TYPO3 checkout.

It is built on the official [`mcp/sdk`](https://packagist.org/packages/mcp/sdk)
and speaks **stdio** (`bin/typo3-cms-mcp`): the MCP client launches it as a
subprocess, so there is no server to host, no network exposure, and no auth to
configure — the process boundary is the trust boundary.

It is a Composer library (`typo3/cms-mcp`) that can either be required as a
dependency of the codebase it supports, or run from a standalone checkout — see
[Install](#install).

## Goal

The server provides MCP-enabled clients with context that is otherwise spread
across project knowledge, TYPO3 core conventions, and official contribution
documentation. Everything it returns is derived from the bundled `knowledge/`
files; the server has no dependency on any project, checkout, or git state.

## Tools

- `typo3_rule_lookup`: searches local TYPO3 core rules and script notes.
- `typo3_script_help`: finds matching notes for TYPO3 core commands.
- `typo3_core_task_brief`: builds a task checklist enriched with matching
  architecture hints and relevant core checks.
- `typo3_core_run_tests_help`: recommends `Build/Scripts/runTests.sh` commands
  by topic.
- `typo3_architecture_hint`: returns architecture hints for TYPO3 core paths or
  task topics, grouped by section.
- `typo3_component_lookup`: looks up backend UI components by name or topic and
  returns canonical markup, variant and sub-component classes, the custom
  property contract, and the styleguide demo and Sass source paths.
- `typo3_icon_lookup`: validates and discovers core icon identifiers (the
  registered T3Icons names), grouped by category, so unknown identifiers are
  caught before runtime.
- `typo3_label_lookup`: searches registered core labels (XLF trans-units) and
  returns the fully qualified `LLL:` reference and English source text, so
  existing labels get reused instead of new keys invented.
- `typo3_commit_message_help`: drafts and checks TYPO3 core commit messages
  against the contribution rules.
- `typo3_make_me_better`: records what was missing, wrong, or unhelpful about an
  answer as a note under `feedback/` (standalone checkout only, see
  [Improvement notes](#improvement-notes)).
- `typo3_feedback_list`: lists those notes, newest first, so they can be worked
  off (standalone checkout only).
- `typo3://core`: resource index for available knowledge documents.
- `typo3://core/{documentId}`: Markdown resource for a single knowledge
  document.

## Layout

```
bin/typo3-cms-mcp  # stdio entrypoint (the client launches it as a subprocess)
src/               # PHP classes (knowledge loading, tools, SDK wiring)
src/ServerFactory.php  # builds the mcp/sdk server
src/bootstrap.php  # locates the Composer autoloader
knowledge/         # the knowledge base (markdown + JSON), the data source
feedback/          # improvement notes left by agents (standalone checkout only)
vendor/            # Composer dependencies (mcp/sdk); gitignored
```

The entrypoint builds the server via `Typo3CmsMcp\ServerFactory`; the tool and
resource logic lives in `src/Tools.php` and `src/Knowledge.php`, driven entirely
by `knowledge/`.

## Install

The package works both ways: as a Composer dependency of another project, and
as a standalone checkout. `src/bootstrap.php` locates the Composer autoloader at
runtime, so no entrypoint needs to know which of the two it is in.

### As a dependency

```bash
composer require typo3/cms-mcp
```

Composer exposes the stdio entrypoint as `vendor/bin/typo3-cms-mcp`. Point the
MCP client of the consuming project at it, for example in its `.mcp.json`:

```json
{
  "mcpServers": {
    "typo3-cms-mcp": {
      "type": "stdio",
      "command": "php",
      "args": ["vendor/bin/typo3-cms-mcp"]
    }
  }
}
```

The knowledge base ships inside the package, so nothing else needs to be
deployed or configured.

### Standalone

Clone the repository and install the dependencies once:

```bash
composer install
```

This is the setup to use when working on the knowledge base itself, since the
`feedback/` tools only exist in a checkout.

## Run locally

Point an MCP client at the binary:

```json
{
  "mcpServers": {
    "typo3-cms-mcp": {
      "type": "stdio",
      "command": "php",
      "args": ["/absolute/path/to/typo3-cms-mcp/bin/typo3-cms-mcp"]
    }
  }
}
```

Quick stdio smoke test from the shell:

```bash
printf '%s\n' \
  '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2025-03-26","capabilities":{},"clientInfo":{"name":"t","version":"1"}}}' \
  '{"jsonrpc":"2.0","id":2,"method":"tools/list"}' \
  | php bin/typo3-cms-mcp
```

## Improvement notes

The knowledge base only grows if the gaps are known, so an agent that hits one
can report it through `typo3_make_me_better`. Every note becomes its own
markdown file under `feedback/`, named after its timestamp and a slug of the
observation:

```
feedback/2026-07-28-133403-typo3-label-lookup-liefert-keine-treffer.md
```

Front matter carries `date`, `category` (`missing-knowledge`, `wrong-answer`,
`tool-gap`, `bug`, `idea`), `status`, and the `tool` the note is about.
`typo3_feedback_list` reads them back, newest first and open ones by default;
set `status: done` in a note once it has been implemented.

One file per note means concurrent agents never write to the same file, and the
filename is always derived from the content — an agent cannot choose it and so
cannot write outside the directory.

Both tools exist **only in a standalone checkout**. Installed as a Composer
dependency the package lives in `vendor/`, where anything written would be lost
on the next `composer install`; there the server stays strictly read-only and
neither tool appears in `tools/list`.

## Knowledge Base

The knowledge base lives in `knowledge/`:

- `knowledge/typo3-core-rules.md`
- `knowledge/typo3-core-scripts.md`
- `knowledge/typo3-core-architecture.md`
- `knowledge/typo3-css-architecture.md`
- `knowledge/typo3-commit-messages.md`
- `knowledge/typo3-contribution-sources.md`
- `knowledge/architecture-hints/` (one JSON file per section, e.g. `css.json`,
  `php.json`, `typescript.json`, `general.json`)
- `knowledge/test-suite-hints.json`
- `knowledge/catalog/` (the lookup catalog: `components.json`, `icons.json`,
  `labels.json`, `component-checklist.json`)

All knowledge files are read fresh on every request, so editing them takes
effect immediately — no restart or rebuild. Tool names, input schemas, and
response formatting live in `src/`. Add new rules or scripts to `knowledge/`
first; promote recurring workflow logic to a tool only when it has earned it.

Useful upstream sources:

- TYPO3 Core Contribution Guide:
  https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/
- TYPO3 Core Commit Message Rules:
  https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/Appendix/CommitMessage.html
