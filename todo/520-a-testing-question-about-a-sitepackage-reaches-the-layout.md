# A testing question about a sitepackage reaches the layout instead

**Serves:** decisions/
**Run:** `bin/cli hints:probe "Set up tests for our site package extension"`

«Set up tests for our site package extension» comes back with
`sitepackage-layout` and `sitepackage-initial-content`, and reaches
`project-extension-tests` nowhere — measured on 2026-08-02 against an extension
path while settling `D-KNW-008`. The four neighbouring phrasings are right:
"how do I test my extension", "add functional tests to the extension", "set up
phpstan for our extension" and "browser tests for the site package" each reach
the cell they are about.

It is a ranking result rather than a storage one. Two hints are named after a
sitepackage and one is not named after testing at all, so "site package" is the
more discriminating pair of terms and "tests" is carried by too many entries to
separate anything — which is `R-ANS-007` working as designed on a corpus where
the word that names the subject is the weaker signal. So the step is not a new
hint: read the probe, then decide between the two shapes that do not add one —
`appliesTo` on `project-extension-tests` carrying the phrasings that name the
package rather than the harness, or the sitepackage hints declaring what they
are not about. Check both against the four phrasings above, because a change
that fixes this one and loses those is a worse answer than the miss.
