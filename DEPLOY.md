# Deploying to Mittwald shared hosting

This server is plain PHP with no build step and no Composer dependency. Deploying
means: upload the files, point a (sub)domain's document root at `public/`, set a
secret token, and connect the client over HTTPS.

## 1. Pick a (sub)domain

Use a dedicated subdomain, e.g. `typo3-mcp.your-domain.example`. In the Mittwald
mStudio, make sure the domain has TLS enabled (Let's Encrypt is automatic).

## 2. Upload the files

Upload the project (via SFTP, rsync, or Git) into the hosting, for example to:

```
/html/typo3-cms-mcp/
  public/
  src/
  knowledge/
```

You do **not** need to upload `config.local.php.example`, `DEPLOY.md`, or `.git`.
You **must** upload `public/`, `src/`, and `knowledge/`.

Example with rsync over SSH:

```bash
rsync -av --delete \
  --exclude '.git' --exclude 'config.local.php' \
  ./ ssh-user@your-host:/html/typo3-cms-mcp/
```

## 3. Set the document root to `public/`

In mStudio, set the document root of the subdomain to the `public/` directory of
the upload, e.g. `/html/typo3-cms-mcp/public`. This keeps `src/` and `knowledge/`
outside the web root. The endpoint is then the domain root:
`https://typo3-mcp.your-domain.example/`.

## 4. Set the auth token

Generate a long random secret:

```bash
openssl rand -hex 32
```

Then provide it in **one** of these ways:

- **Environment variable** (preferred if your hosting lets you set env vars):
  `MCP_AUTH_TOKEN=<secret>`.
- **Local config file**: copy the example and edit it (it is gitignored):

  ```bash
  cp config.local.php.example config.local.php
  # set 'auth_token' => '<secret>'
  ```

  Place `config.local.php` in the project root (one level above `public/`).

With no token configured the server returns HTTP 500 for every request, so it is
never accidentally open.

## 5. Verify

```bash
# Unauthorized without the token:
curl -s -o /dev/null -w "%{http_code}\n" -X POST https://typo3-mcp.your-domain.example/
# -> 401

# Tool list with the token:
curl -s -X POST https://typo3-mcp.your-domain.example/ \
  -H "Authorization: Bearer <secret>" -H "Content-Type: application/json" \
  -d '{"jsonrpc":"2.0","id":1,"method":"tools/list"}'
```

If the second call returns `401` even with the right token, the Authorization
header is being stripped by the host. The bundled `public/.htaccess` restores it
for both mod_rewrite and CGI/FastCGI setups; make sure `.htaccess` was uploaded
and `AllowOverride` permits it (default on Mittwald Apache).

## 6. Connect the client

Add to `~/.claude.json` (user scope) or a project `.mcp.json`:

```json
{
  "mcpServers": {
    "typo3-cms-mcp": {
      "type": "http",
      "url": "https://typo3-mcp.your-domain.example/",
      "headers": {
        "Authorization": "Bearer <secret>"
      }
    }
  }
}
```

Reload the VS Code window (or restart the Claude Code session) and run `/mcp` to
confirm `typo3-cms-mcp` is connected with its tools.

## Updating

Re-upload changed files. Editing anything under `knowledge/` takes effect on the
next request — no restart, no build. There is no persistent process to manage.
