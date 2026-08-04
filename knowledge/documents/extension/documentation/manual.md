---
description: >-
  The files an extension's Documentation/ directory consists of, whole, and the command that renders it before it is published.
whenToUse: >-
  When an extension has no manual yet, or has one that predates guides.xml. What a manual is for and where it lives is the hint below; this is what goes in the directory.
hints:
  - extension-documentation
---

# Setting Up an Extension Manual

Two files make a directory a manual: `Documentation/Index.rst` as the entry
point and `Documentation/guides.xml` as the renderer configuration. Everything
else is convention, and docs.typo3.org renders the tree from those two.

## Documentation/guides.xml

```xml
<?xml version="1.0" encoding="UTF-8"?>
<guides xmlns="https://www.phpdoc.org/guides"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="https://www.phpdoc.org/guides ../vendor/phpdocumentor/guides-cli/resources/schema/guides.xsd">
    <extension class="\T3Docs\Typo3DocsTheme\DependencyInjection\Typo3DocsThemeExtension"/>
    <project title="My Extension"
             version="local"
             copyright="since {year} by the extension's authors &amp; contributors"
    />
</guides>
```

The namespace, the `class` on `extension` and `title` on `project` are what the
renderer needs; the rest is optional. `version` is set during the deployment
that publishes the manual, so `local` is what a repository carries. The
`extension` element takes the attributes that produce the edit link and the
project header — `edit-on-github`, `edit-on-github-directory`, `project-home`,
`project-issues`, `interlink-shortcode` — and an extension that wants none of
them leaves them out.

Two files that look like this one are not the template. A core system
extension's `guides.xml` points `edit-on-github` at `typo3/typo3` and carries
`typo3-core-preferred`, which is the core repository's arrangement rather than
an extension's. And an extension whose manual predates the current renderer
still ships `Settings.cfg`; that file configures the renderer that was replaced,
and copying it produces a manual nothing renders.

## Documentation/Index.rst

```rst
.. include:: /Includes.rst.txt

..  _start:

============
My Extension
============

:Extension key:
   my_extension

:Package name:
   vendor/my-extension

:Version:
   |release|

:Language:
   en

:License:
   This document is published under the
   `Open Publication License <https://www.opencontent.org/openpub/>`__.

:Rendered:
   |today|

----

One sentence on what the extension does.

----

**Table of Contents:**

.. toctree::
   :maxdepth: 2
   :titlesonly:

   Introduction/Index
   Installation/Index
   Configuration/Index
```

The `toctree` is what pulls the rest of the tree in: one CamelCase directory per
chapter, each with its own `Index.rst`, named without the file extension. A
chapter directory that no `toctree` names is not rendered.

## The two conventional files

`Includes.rst.txt` is included by every page and holds what has to appear on all
of them. It is optional for an extension and the official manuals all carry one,
which is why the include line stands at the top of `Index.rst` above.

`Sitemap.rst` is a nearly empty file the renderer fills while it runs, listed in
a hidden `toctree` at the foot of `Index.rst`.

## Rendering it before it is published

```bash
docker run --rm --pull always -v $(pwd):/project -it \
  ghcr.io/typo3-documentation/render-guides:latest --config=Documentation
```

`--config` names the directory holding `guides.xml`, and the rendered HTML lands
below `Documentation-GENERATED-temp/`, which belongs in `.gitignore`.

A run whose last line reads "Successfully placed" has not said that everything
rendered. A tree whose `Index.rst` includes a file that is not there logs the
failed directive and exits 0 — `--fail-on-log` is what makes the same run exit
1, and it is what a check calls. `--fail-on-error` is the narrower one and lets
a warning pass. In CI the command also takes `--no-progress`, and
`--minimal-test` renders single-page where the check only has to establish that
the tree builds.

That include is the failure to expect first: `Index.rst` opens with
`.. include:: /Includes.rst.txt` in every manual worth copying from, and the
file it names is one an extension has to carry itself.
