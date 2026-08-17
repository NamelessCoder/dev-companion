# State what a Composer installation generates below the document root

**Serves:** feedback/2026-08-17-211141-nothing-enumerates-what-a-composer-install.md
**Priority:** normal

Step 1a, judged on 2026-08-18 in `D-KNW-088`: `project-build-and-scripts` defers
to the `.gitignore` and names no path, `_assets_install` occurs nowhere below
`knowledge/`, and a query for it returns `public-assets`, which answers about
`_assets` and is silent about the sibling. Establish which paths a Composer
installation generates below the document root and the project root, on each
covered major — the core half from `.checkouts/`, where
`Install/Classes/FolderStructure/DefaultFactory` and
`FolderStructureTemplateFiles/` place the generated `.htaccess` files and
`DefaultSystemResourcePublisher` carries `_assets_install/` from 14; the rest
from `typo3/cms-base-distribution` and `typo3/cms-composer-installers`, which
`bin/cli environment:create E-SITE` installs. Then write the set into
`project-build-and-scripts` as the ignore file it is read off, bound with
`since` where a path is not on every covered major, and name `_assets_install`
on `public-assets` beside the `_assets` it already explains. The commit archives
the feedback with `bin/cli feedback:archive`.
