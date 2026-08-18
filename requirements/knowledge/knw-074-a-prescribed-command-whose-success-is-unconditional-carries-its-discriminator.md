---
id: R-KNW-074
status: held
restsOn: [D-KNW-093]
---

# R-KNW-074 — A prescribed command whose success is unconditional carries its discriminator

**Where a hint prescribes a command that reports success without having acted,
the hint says what a correct result looks like outside that command's own
output.**

In this domain a success message is not evidence, and the hint that hands the
caller the command is the only place the discriminator is read — nothing carries
it there from a warning elsewhere in the corpus. What it names sits outside the
command: the artifact, the database, the directory the command was meant to
write.

The rule is bounded to a command whose success is unconditional, read off that
command's own class rather than off its message. A command that reports its own
failures gets nothing, because a discriminator there warns about a trap the
reader is not walking into, and every sentence is paid for in the answer that
carries the hint —
[`R-KNW-024`](knw-024-a-check-is-offered-only-where-the-command-exists.md) is
the same economy on the other side.

Its neighbour is
[`R-KNW-049`](knw-049-a-check-that-can-pass-without-reading-anything-says-so.md):
that one keeps a check that inspected nothing from being read as a check that
ran, and this one keeps a command that acted on nothing from being read as a
command that acted.

## From

A session building a demo site that hit four of them in three quarters of an
hour — `impexp:export` shipping no image bytes, the same command writing to a
path it was not given, `extension:setup` migrating from a cached TCA, and
`DataHandler` discarding every inline relation — and paid between two and twelve
round trips for each
(`feedback/2026-08-17-212800-four-commands-reported-success-while-doing.md`,
2026-08-17). The sweep those four earned found `language:update`,
`backend:user:create` and `upgrade:run` answering the same way.

## Held by

- `HintsTest::aPrescribedCommandWhoseSuccessIsUnconditionalCarriesItsDiscriminator`
