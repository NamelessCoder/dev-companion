---
date: 2026-08-02T14:49:02+00:00
category: missing-knowledge
status: closed
closed: 2026-08-03
model: claude-opus-5[1m]
tool: typo3_architecture_lookup, typo3_task_guide
directory: /home/benji/projects/typo3-cms
---

# Task: fix Forge #105403 in the Fluid image ViewHelpers.

## Observation

Task: fix Forge #105403 in the Fluid image ViewHelpers.

I asserted twice, as an argument for why a package resource has to be wrapped into a "fake" FAL record, that image processing requires a FAL object and that without one nothing can determine the image dimensions. The user corrected me — "das Processing hier ist eins von vielvielen, wir haben auch noch GraphicalFunctions als zweiten Weg" — and the checkout showed both claims were wrong:

- LocalImageProcessor::processTask() calls $task->getSourceFile()->getForLocalProcessing(false) as its first step and hands a plain absolute path downwards. The FAL object is unwrapped immediately.
- GraphicalFunctions::resize(string $sourceFile, ...), imageMagickConvert($imagefile, ...) and getImageDimensions(string $imageFile, ...) all take path strings. Nothing below the task layer knows about FAL.
- So for an EXT: resource the full path is: absolute package path, to a URI relative to the public dir, to a storage-0 identifier, to a File object, to a ProcessedFile task, back to an absolute local path via getForLocalProcessing(), into GraphicalFunctions. The path goes out and comes back; everything in between is ceremony.
- getImageDimensions(string) also disproves my second claim, that dimensions are unavailable without a FAL record.
- ContentObjectRenderer::getImgResource() and GIFBUILDER are further entry points that have always taken paths and EXT: syntax.

Both wrong assertions were load-bearing: they were the reason I described the fake FAL record as technically necessary rather than historical, which shaped what I told the user was and was not fixable. ResourceFactory carries two @todo comments saying the fallback storage is to be removed "asap" — the source itself contradicts the necessity reading, and I had read that file.

## Query

Reasoning about whether image processing requires a FAL object, checked against typo3/sysext/core/Classes/Resource/Processing/LocalImageProcessor.php, AbstractTask.php, ImageCropScaleMaskTask.php and typo3/sysext/core/Classes/Imaging/GraphicalFunctions.php on 15.0.0-dev

## Suggestion

Add hints for the image processing pipeline: that FAL is one entry point among several rather than the foundation, that the processing task layer unwraps to a local path via getForLocalProcessing() before any work happens and everything below it operates on path strings, that GraphicalFunctions exposes resize/imageMagickConvert/mask/getImageDimensions on paths, and that ContentObjectRenderer::getImgResource() and GIFBUILDER are separate path-based entry points. A session reasoning about what can be changed in this area will otherwise infer necessity from the only call path it happens to read, as I did.

The EXT: half of this ask is answered and is struck. `fal-storages-drivers` states that an EXT: path resolves through the storage-0 fallback and that the source marks the route for removal, and `fluid-resource-uris` states that f:image and f:uri.image resolve through FAL and the Extbase ImageService rather than through the System Resource API. Both were written on 2026-08-03, the day after this was filed, and both are reachable on an image-shaped query. `D-KNW-042` has the readings.
