# TYPO3 CMS MCP

A remote MCP server (plain PHP) that exposes a curated TYPO3 core contribution **knowledge base**: contribution rules, core script and `runTests.sh` notes, architecture hints, and commit message conventions. It is read-only knowledge — it does not inspect, read, or run anything against a TYPO3 checkout.

It speaks the MCP **Streamable HTTP** transport in a stateless way (one PHP request per JSON-RPC message), so it runs on classic PHP shared hosting with no persistent process. Access is protected by a static bearer token.

## Goal

The server provides MCP-enabled clients with context that is otherwise spread across project knowledge, TYPO3 core conventions, and official contribution documentation. Everything it returns is derived from the bundled `knowledge/` files; the server has no dependency on any project, checkout, or git state.

## Tools

- `typo3_rule_lookup`: searches local TYPO3 core rules and script notes.
- `typo3_script_help`: finds matching notes for TYPO3 core commands.
- `typo3_core_task_brief`: builds a task checklist enriched with matching architecture hints and relevant core checks.
- `typo3_core_run_tests_help`: recommends `Build/Scripts/runTests.sh` commands by topic.
- `typo3_architecture_hint`: returns architecture hints for TYPO3 core paths or task topics, grouped by section.
- `typo3_commit_message_help`: drafts and checks TYPO3 core commit messages against the contribution rules.
- `typo3://core`: resource index for available knowledge documents.
- `typo3://core/{documentId}`: Markdown resource for a single knowledge document.

## Layout

```
public/index.php   # the single MCP HTTP endpoint (web document root)
public/.htaccess   # routing + Authorization header pass-through (Apache)
src/               # PHP classes (knowledge loading, tools, MCP dispatch)
knowledge/         # the knowledge base (markdown + JSON), the data source
config.local.php   # local secret (gitignored); see config.local.php.example
```

There is no build step and no Composer dependency.

## Run locally

```bash
MCP_AUTH_TOKEN=dev-secret php -S 127.0.0.1:8765 -t public
```

Smoke test:

```bash
curl -s -X POST http://127.0.0.1:8765/ \
  -H "Authorization: Bearer dev-secret" -H "Content-Type: application/json" \
  -d '{"jsonrpc":"2.0","id":1,"method":"tools/list"}'
```

The auth token is read from the `MCP_AUTH_TOKEN` environment variable, or from a
gitignored `config.local.php` (copy `config.local.php.example`). With no token
configured the server refuses every request, so it is never accidentally open.

## Client configuration

Point an MCP client at the deployed HTTPS URL with the bearer token:

```json
{
  "mcpServers": {
    "typo3-cms-mcp": {
      "type": "http",
      "url": "https://your-domain.example/",
      "headers": {
        "Authorization": "Bearer YOUR_TOKEN"
      }
    }
  }
}
```

See [DEPLOY.md](DEPLOY.md) for deploying to Mittwald shared hosting.

## Knowledge Base

The knowledge base lives in `knowledge/`:

- `knowledge/typo3-core-rules.md`
- `knowledge/typo3-core-scripts.md`
- `knowledge/typo3-core-architecture.md`
- `knowledge/typo3-css-architecture.md`
- `knowledge/typo3-commit-messages.md`
- `knowledge/typo3-contribution-sources.md`
- `knowledge/architecture-hints/` (one JSON file per section, e.g. `css.json`, `php.json`, `typescript.json`, `general.json`)
- `knowledge/test-suite-hints.json`

All knowledge files are read fresh on every request, so editing them takes effect
immediately — no restart or rebuild. Tool names, input schemas, and response
formatting live in `src/`. Add new rules or scripts to `knowledge/` first; promote
recurring workflow logic to a tool only when it has earned it.

Useful upstream sources:

- TYPO3 Core Contribution Guide: https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/
- TYPO3 Core Commit Message Rules: https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/Appendix/CommitMessage.html
