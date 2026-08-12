---
id: D-KNW-042
date: 2026-08-03
status: open
---

# D-KNW-042 — What the image pipeline does below the task layer is a gap this server owns

**Where FAL stops in the image pipeline is inside this server's boundary and
missing from it, so the feedback is trimmed to that half and queued at
`normal`.**

The task layer unwraps to a local path and everything below it takes path
strings, and nothing in the corpus says so. What it says about images instead
stops at a FAL boundary each time, without saying that the boundary is one — so
a session asking whether FAL is required reads the last thing it is told as the
foundation. This one did, twice, as an argument for what could not be changed.

## Evidence

- Re-run on 2026-08-03 against the corpus as it is now. `bin/cli hints:probe`
  with the feedback's own query reaches `fal-processing` at
  `appliesTo(16) + text(211)` and `fal-basics` at `appliesTo(3) + text(294)`,
  and nothing else. `fal-processing` stops at the processor by its own title —
  "Which Processor Claims a File, and in What Order" — and neither hint says
  what a processor does with the file once it has claimed it.
- Neither hint existed when the feedback was filed. `fal-processing` was written
  by `b1d6418` on 2026-08-03 and the feedback is stamped 2026-08-02T14:49:02, so
  the corpus did not mislead this session. It was silent, and after a day of
  work on exactly this subject it is still silent on the half the session got
  wrong.
- The words are absent. `GraphicalFunctions`, `getForLocalProcessing`,
  `imageMagickConvert`, `getImgResource` and `GIFBUILDER` occur nowhere below
  `knowledge/` or `skills/`.
- What the feedback claims about TYPO3 holds, read in `.checkouts/main` at
  `c71b2bdb2f`, 15.0.0-dev. `LocalImageProcessor::processCropScaleMask()` is the
  single line `$task->getSourceFile()->getForLocalProcessing(false)`, and
  everything under it takes `string $originalFileName`; `processPreview()` does
  the same at line 382. `GraphicalFunctions` declares
  `resize(string $sourceFile, …)`, `imageMagickConvert($imagefile, …)`,
  `mask(string $inputFile, …)` and `getImageDimensions(string $imageFile, …)`,
  all on paths. `ContentObjectRenderer::getImgResource()` is at line 3332 and
  `Frontend\Imaging\GifBuilder` at line 54 of its own file.
- One correction to the report, which changes nothing it concluded. The unwrap
  is not the first step of `processTask()`: that method delegates through
  `processTaskWithLocalFile()`, and the unwrap is in the two helpers below it.
- A second session reached for the same class from a different task.
  [`R-KNW-048`](../../requirements/knowledge/knw-048-which-processor-claims-a-file-is-answered.md)
  records a patch review of 2026-08-01 that read `GraphicalFunctions` by hand
  among seven core classes, because nothing below `knowledge/` said which of
  them runs when. That review wanted the order and this session wanted the
  signature, and neither could reach the file at all.
- The `EXT:` half of the suggestion is answered, by two hints also written on
  2026-08-03. `fal-storages-drivers` states at `since: 14` that uid 0 is the
  fallback, that `ResourceFactory::retrieveFileOrFolderObject()` resolves an
  `EXT:` path through it, and that the source marks the route for removal — the
  two `@todo` comments are still there, at lines 195 and 212.
  `fluid-resource-uris` states at `since: 14` that `f:image` and `f:uri.image`
  are not on the System Resource API and resolve through FAL and the Extbase
  ImageService.
- Both are reachable on an image-shaped query.
  `bin/cli hints:probe "ImageViewHelper src EXT: package resource"` returns
  `fal-storages-drivers` at `text only(109)`, and
  `"f:image with an EXT: path resolves through the fallback storage"` returns
  `fluid-resource-uris` at `text only(101)`. So this is not step 2: the
  statement is placed where the task passes.
- It sits just below the boundary
  [`D-KNW-028`](knw-028-how-a-file-becomes-a-processed-one-is-a-gap-this-server-owns.md)
  drew. That entry took the dispatch — which processor claims a file — and left
  what the claim leads to unsaid, which is the line this feedback walks up to
  from the other side.

## Decided

- Step 1a on the pipeline half, and queued rather than closed on the spot. What
  lands is a statement about TYPO3 read across `.checkouts/`, and this run read
  one branch to check four assertions.
- Trimmed rather than archived. The `EXT:` and storage-0 part of the suggestion
  is in the corpus and reachable, so it is struck from the feedback with the two
  hints that answer it named in its place; the rest stays open behind the card.
- `normal` rather than the `low` the card arrived at. Two sessions from two task
  shapes reached for `GraphicalFunctions` and found nothing, and the assertions
  this one built on the silence were load-bearing for what it told its user was
  fixable.
- Not `high`. Nothing is blocked on it, the subject is half covered already, and
  the corpus states no falsehood — it stops early.
- Not the suggestion's own wording. Its author read four core classes for one
  task and was guessing about this repository, as
  [`judging.md`](../../documentation/records/judging.rst) says every suggestion
  is.
- Where the statement goes is the todo's. `fal-processing` is the candidate it
  starts from, and whether the entry points beside FAL belong there or in a hint
  of their own needs the reading that produces the statement.

## Assumed

- That the silence is what produced the wrong assertion, rather than the model's
  own prior. Nothing distinguishes the two from here, and the lever is the same
  either way.
- That the path-based entry points are worth stating at all. They are old API a
  session may never touch, and what makes them evidence is the inference they
  disprove rather than a caller expected to use them.
- That one session wrote this feedback and the ten beside it. They share a
  directory, a model, a subject and nine minutes, and nothing in a feedback
  records a session.

## Wrong if

- The unwrap point differs across `.checkouts/12.4`, `13.4` and `14.3`. The todo
  plans one statement with a range, and it would then be one statement per line.
- `getImgResource()` or `GifBuilder` turns out to be on the way out in 15. "FAL
  is one entry point among several" would then be a statement about the past,
  and the hint would be teaching a route nobody should take.
- The sibling `feedback/2026-08-02-144814`, in hand on its own branch today,
  lands here as well. The two would then be one gap carried by two cards, and
  one of them should carry both under a `**Serves:**` line naming the other.
- A session reads the written statement and still argues from FAL necessity. The
  gap would be in the routing rather than in the corpus, and this entry would
  have answered the cheap rung of the ladder for a step-3 problem.

## Since then

Written on 2026-08-03 into `fal-processing`, read across all four checkouts, and
one premise the todo carried is wrong. `ContentObjectRenderer::getImgResource()`
and `GIFBUILDER` are not entry points that never hold a FAL object: a path or an
`EXT:` path goes through `ResourceFactory::retrieveFileOrFolderObject()` and is
processed as a `File`, on `12.4` at line 3802 as on `main` at 3373, and
`GifBuilder`'s own `IMAGE` and mask files come back through `getImgResource()`
by way of its `getResource()`. They take paths, which is what the feedback saw,
and they wrap them. What is reached without FAL is `GraphicalFunctions` itself,
so the hint states the correction rather than the suggestion's wording. The
conclusion the feedback drew from them stands: the necessity reading is still
disproved, by the task layer and by `getImageDimensions(string)`.

The first **Wrong if** half fires. The unwrap call is
`$task->getSourceFile()->getForLocalProcessing(false)` on all four, but the
class it sits in moved: `LocalCropScaleMaskHelper` and `LocalPreviewHelper` on
`12.4` and `13.4`, folded into `LocalImageProcessor` on `14.3` and `main`, where
both helper files are gone. `GraphicalFunctions` moved with it — `resize()` and
`mask()` do not exist on `12.4`, where `imageMagickConvert()` is the entry and
`getImageDimensions()` takes no result-object flag. So it is one statement and
two bound pairs rather than one statement per line. The second **Wrong if** does
not fire: neither `getImgResource()` nor `GifBuilder` carries a deprecation on
`14.3` or `main`.

Where it went was `fal-processing` rather than a hint of its own, which this
entry left to the reading. The query that produced the feedback already lands
there first — `appliesTo(16) + text(290)` after the change — and a session
reading the dispatch is the one that infers necessity, so the correction sits
where it cannot be missed. The title now names both halves.
[`R-KNW-054`](../../requirements/knowledge/knw-054-where-fal-stops-in-the-image-pipeline-is-answered.md)
holds it.
