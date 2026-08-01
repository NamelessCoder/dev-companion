# Give PHPStan a result cache that is not shared between worktrees

**Serves:** documentation/feedback/

`phpstan.neon` declares no `tmpDir`, so every checkout on the machine writes its
result cache to `/tmp/phpstan`, and the cache stores absolute paths. On
2026-08-02 all three parallel sessions independently lost time to the same
internal error, each naming a phar under
`.worktrees/try-d-dis-1-against-a-root-that-is-also-in-vendor` — a worktree that
no longer existed. `vendor/bin/phpstan clear-result-cache` clears it for whoever
runs it and the next worktree poisons it again. Set `tmpDir` in `phpstan.neon`
to a path below the checkout, `var/phpstan` beside the other generated things,
and gitignore it; then check that two worktrees can run `composer stan` at once.
This is not a parallel-work item only — one stale entry breaks `composer stan`
for every checkout on the machine, worktree or not.
