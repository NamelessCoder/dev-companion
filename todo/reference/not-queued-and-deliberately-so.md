# Not queued, and deliberately so

Things a session may otherwise rediscover and mistake for work:

- **`REVIEW-03`** needs a core checkout with actual uncommitted changes.
  `/home/benji/projects/typo3-cms` is on `main` with a clean tree, so the
  review has nothing to read. It needs a patch in progress before it can run at
  all.
- **The catalog roadmap** — an API signature lookup, a changelog scaffold, a
  test scaffold, and the structured-output envelope that needs a spike of
  `vendor/mcp/sdk` first. None of it is blocked; none of it serves an open
  feedback or a forward review either, which is why it is below everything that
  does.
- **`phpstan/phpstan` 2.2.6 → 2.2.7 and `phpunit` 11.5 → 12.5.** Ordinary
  maintenance, not an item.
