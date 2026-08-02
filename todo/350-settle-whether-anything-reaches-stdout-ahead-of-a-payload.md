# Settle whether anything reaches stdout ahead of a payload

**Serves:** decisions/, R-ANS-008

`Typo3Cli::decode()` tries the whole output, then the substring from the first
`{` or `[`, and stops — so a payload with a brace-bearing line in front of it is
thrown away. `D-DIS-003` was corrected on 2026-08-01 for the half that does not
need a producer, and the producer is what stayed open: Xdebug's connection
warning goes to stderr through `php_log_err()`, and a PHP deprecation is
swallowed by `ErrorHandler::handleError()` returning `true` before PHP can print
it. Both were measured, and neither reaches the decoder. The one candidate left
unmeasured is the transport: whether `ddev exec` folds the container's stderr
into its own stdout, which would put every stderr line this server currently
reads separately in front of the payload. Start a DDEV project and run something
that writes to both streams, reading the two pipes apart the way
`Typo3Cli::execute()` does — no project on this machine was running on
2026-08-01. Where it folds them, make `decode()` try each `{` and `[` offset in
turn and hold it with a `Typo3CliTest` case; where it does not, record in
`D-DIS-003` that nothing known reaches that stream and leave the decoder alone.
