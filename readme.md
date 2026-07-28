# TYPO3 CMS MCP

A local MCP server (plain PHP) that gives MCP-enabled clients a curated TYPO3
core contribution **knowledge base**: contribution rules, the Gerrit workflow,
core script and `runTests.sh` notes, architecture hints, commit message
conventions, and a catalog of backend UI components, icon identifiers, and
registered labels — context that is otherwise spread across project knowledge,
core conventions, and the official contribution documentation.

Everything it answers comes from the bundled `knowledge/` files. It is read-only
knowledge with no dependency on any project, checkout, or git state: it does not
inspect, read, or run anything against a TYPO3 checkout.

It is built on the official [`mcp/sdk`](https://packagist.org/packages/mcp/sdk)
and speaks **stdio** (`bin/typo3-cms-mcp`): the MCP client launches it as a
subprocess, so there is no server to host, no network exposure, and no auth to
configure — the process boundary is the trust boundary.

## Install

Requirements: **PHP 8.2+** and Composer. The package works both ways — as a
standalone checkout and as a Composer dependency of another project.

### Standalone

Clone the repository and install the dependencies once:

```bash
composer install
```

Then point an MCP client at the entrypoint with an absolute path, for example in
its `.mcp.json`:

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

This is the setup to use when working on the knowledge base itself, since the
`feedback/` tools only exist in a checkout.

### As a dependency

The package is not published on Packagist, so it is required from a local
checkout (or a Git URL) through a `repositories` entry in the consuming
project's `composer.json`:

```json
{
  "repositories": [
    { "type": "path", "url": "/absolute/path/to/typo3-cms-mcp" }
  ]
}
```

```bash
composer require typo3/cms-mcp
```

Composer then exposes the stdio entrypoint as `vendor/bin/typo3-cms-mcp`, which
is what the consuming project's MCP client is pointed at:

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

### Smoke test

Two JSON-RPC lines on stdin are enough to see the server come up and list its
tools:

```bash
printf '%s\n' \
  '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2025-03-26","capabilities":{},"clientInfo":{"name":"t","version":"1"}}}' \
  '{"jsonrpc":"2.0","id":2,"method":"tools/list"}' \
  | php bin/typo3-cms-mcp
```

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
  existing labels get reused instead of new keys invented; `mode: domains` lists
  the registered translation domains instead.
- `typo3_commit_message_help`: drafts and checks TYPO3 core commit messages
  against the contribution rules.
- `typo3_make_me_better`: records what was missing, wrong, or unhelpful about an
  answer as a note under `feedback/` (standalone checkout only, see
  [Improvement notes](#improvement-notes)).
- `typo3_feedback_list`: lists those notes, newest first, so they can be worked
  off (standalone checkout only).

## Resources

- `typo3://core`: resource index for available knowledge documents.
- `typo3://core/{documentId}`: Markdown resource for a single knowledge
  document, for example `typo3://core/typo3-core-rules`.

## Knowledge base

Everything the tools and resources answer from lives in `knowledge/`. The
markdown documents are what `typo3://core/{documentId}` serves; the JSON files
drive the individual tools:

- `typo3-core-rules.md`
- `typo3-core-scripts.md`
- `typo3-core-architecture.md`
- `typo3-css-architecture.md`
- `typo3-commit-messages.md`
- `typo3-gerrit-workflow.md`
- `typo3-contribution-sources.md`
- `architecture-hints/` (one JSON file per section: `css.json`, `php.json`,
  `typescript.json`, `general.json`)
- `catalog/` (the lookup catalog: `components.json`, `icons.json`,
  `labels.json`, `component-checklist.json`)
- `test-suite-hints.json`, `task-intents.json`

All knowledge files are read fresh on every request, so editing them takes
effect immediately — no restart or rebuild.

Useful upstream sources:

- TYPO3 Core Contribution Guide:
  https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/
- TYPO3 Core Commit Message Rules:
  https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/Appendix/CommitMessage.html

## Improvement notes

The knowledge base only grows if the gaps are known, so an agent that hits one
reports it through `typo3_make_me_better`. Every note becomes its own markdown
file under `feedback/`; `typo3_feedback_list` reads them back, newest first. A
note is closed by deleting it in the commit that implements the improvement, so
`feedback/` only ever holds open items.

Both tools exist **only in a standalone checkout**. Installed as a Composer
dependency the package lives in `vendor/`, where anything written would be lost
on the next `composer install`; there the server stays strictly read-only and
neither tool appears in `tools/list`.

Working on this repository — layout, conventions, and how notes are worked off —
is documented in [CLAUDE.md](CLAUDE.md).
