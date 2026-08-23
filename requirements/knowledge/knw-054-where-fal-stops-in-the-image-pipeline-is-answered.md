---
id: R-KNW-054
title: 'Where FAL stops in the image pipeline is answered'
status: held
restsOn: [D-KNW-042]
heldBy:
  - HintsTest::whereFalStopsInTheImagePipelineIsAnswered
---

# R-KNW-054 — Where FAL stops in the image pipeline is answered

**What runs below the processing task is answered: the unwrap to a local path,
the path-based API under it, and which entry points are not a way past FAL.**

A session reasoning about what can be changed in this area infers necessity from
the one call path it happens to read. The corpus answered up to the processor
and stopped there, so the last thing it said was read as the foundation.

## From

A session asserted twice, as its reason for calling a wrapped package resource
technically necessary, that image processing requires a FAL object and that
dimensions are unavailable without one (2026-08-02, judged as `D-KNW-042`).
