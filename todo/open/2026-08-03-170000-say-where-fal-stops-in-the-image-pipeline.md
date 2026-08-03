# Say where FAL stops in the image processing pipeline

**Serves:** feedback/2026-08-02-144902-task-fix-forge-105403-in-the-fluid-image.md
**Priority:** normal

Step 1a of the ladder, on the evidence in
[`D-KNW-042`](../../decisions/knowledge/knw-042-what-the-image-pipeline-does-below-the-task-layer-is-a-gap-this-server-owns.md):
`fal-processing` answers which processor claims a file and stops there, so a
session asking whether image processing needs a FAL object reads the last thing
the corpus tells it as the foundation. Read
`LocalImageProcessor::processCropScaleMask()` and `processPreview()` for where
`getForLocalProcessing()` unwraps the task's source file, the signatures of
`resize()`, `imageMagickConvert()`, `mask()` and `getImageDimensions()` on
`Core\Imaging\GraphicalFunctions`, and `ContentObjectRenderer::getImgResource()`
with `Frontend\Imaging\GifBuilder` as the entry points that never hold a FAL
object — on `.checkouts/12.4`, `.checkouts/13.4`, `.checkouts/14.3` and
`.checkouts/main`, for whether the unwrap point moved between the majors. Then
write it beside the dispatch statements on `fal-processing` in
`knowledge/hints/fal.json`, with a version range only where the four differ, and
a requirement for what has to keep holding.
