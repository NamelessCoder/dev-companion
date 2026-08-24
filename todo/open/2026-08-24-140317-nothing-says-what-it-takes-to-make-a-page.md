# Say where a development installation gets the page object that renders the package

**Serves:** feedback/2026-08-24-140317-nothing-says-what-it-takes-to-make-a-page.md
**Priority:** normal

Judged on 2026-08-25 as `D-KNW-118`: step 1a, queued. `site-sets`,
`installation-setup` and `fresh-instance-seeding` each carry one way a page
comes to render, all three answering their own question, and nothing puts them
under the one a package that ships no page TypoScript has.

Read on `.checkouts/13.4`, `14.3` and `main` what a development installation of
such a package can be given a page object by, starting from what
[`D-KNW-116`](../../decisions/knowledge/knw-116-the-page-object-typo3-setup-leaves-behind-is-a-subject-this-server-owns.md)
established: the installation already has one, so the subject is replacing it
rather than adding one. Establish whether a set shipped by a development-only
extension is reached at all while the site's own `setup.typoscript` stands on
`14.3`, and what the clearing `sys_template` row does to it on `13.4` — the
shape the feedback recommends is a set, and both of those are silent at HTTP
200.

Then establish where such a package lives so it is not released. The feedback
says `Build/` being export-ignored keeps it out; `extension-ter-release` states
the artefact is decided by Tailor's `conf/ExcludeFromPackaging.php` and never by
`.gitattributes`, so which packaging path honours the attribute is read before
anything is written. The same reading settles whether the directory an extension
is developed in has to carry its key at all, or whether a Composer path
repository names the package instead.

The statement goes where a caller reaches it from the question rather than from
the symptom, and step 4 of `typo3-development-installation` gains the line that
routes to it: that step is content today and a page object is not content, which
is why the session read its routing as being about distributions.
