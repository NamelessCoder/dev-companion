---
id: R-KNW-048
title: 'Which processor claims a file is answered'
status: held
---

# R-KNW-048 — Which processor claims a file is answered

**How a file becomes a processed one is answered: the order the registry asks
in, and the first `canProcessTask()` that says yes.**

The order is the whole of it. A processor registered after the one that already
claims a case is never reached, and nothing says so at the point where it is
registered.

## From

A patch review replacing GD read seven core classes by hand —
`GraphicalFunctions`, `LocalImageProcessor`, `SvgImageProcessor`,
`ThumbnailViewHelper`, `DeferredBackendImageProcessor`, `PreviewProcessing` and
`PreviewNotAvailable.svg` — because nothing below `knowledge/` said which of
them runs when (2026-08-01, judged as `D-KNW-028`).

## Held by

- `HintsTest::whichProcessorClaimsAFileIsAnswered`
