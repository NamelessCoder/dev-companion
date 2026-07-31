---
date: 2026-07-31T14:23:47+02:00
category: bug
status: open
tool: typo3_extension_scope
---

# `Icons.php` built in a loop reports `provider` and `source` as icons

## Observation

`typo3_extension_scope` for `georgringer/news` returned 26 icon identifiers.
Two of them do not exist:

```
"icons":["apps-pagetree-folder-contains-news", …, "ext-news-addon","provider","source"]
```

The extension's `Configuration/Icons.php` builds its list in a loop:

```php
$iconList = [];
foreach ([
    'apps-pagetree-folder-contains-news' => 'ext-news-folder-tree.svg',
    …
] as $identifier => $path) {
    $iconList[$identifier] = [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:news/Resources/Public/Icons/' . $path,
    ];
}
return $iconList;
```

`PhpArray::keys()` counts `[` and `]` across the whole file and takes every
string key it finds at depth 1. The `foreach` array closes and the depth returns
to zero, so the second literal — the one describing a single icon rather than
the list — is read at depth 1 as well and contributes its two keys. Any file
with two array literals at the same bracket depth has the same problem; a loop
is only the most common way to get one.

The `REVIEW-02` run that surfaced this caught it and said so in its coverage
table — "*Parser limitation:* `typo3_extension_scope` reported `provider` and
`source` as icon identifiers; these are artifacts of that dynamic loop, not
registrations". That it was caught is luck of a careful reader: the two names
are plausible enough that a run comparing icons against `Resources/Public/Icons`
would report two registered icons whose files are missing, which is a defect
that does not exist.

## Query

Review this TYPO3 extension. Tell me the most important things that would
prevent us maintaining and supporting it confidently, in priority order. Do not
change files.

## Suggestion

Take the keys of *one* array rather than of every array at a depth: track the
literal that is being read and stop at its closing bracket, so a second literal
later in the file is a separate result rather than more of the first. Where a
file cannot be read statically — the identifiers here are only knowable by
running the loop — returning nothing is a better answer than returning the
inner keys, because an empty list reads as "not determinable" and `provider`
reads as an icon. A fixture with a loop-built `Icons.php` holds it.
