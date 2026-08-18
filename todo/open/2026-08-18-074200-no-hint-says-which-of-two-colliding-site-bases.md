# Say which of two colliding site bases wins, from the 404 that shows it

**Serves:** D-KNW-097, feedback/2026-08-18-074200-no-hint-says-which-of-two-colliding-site-bases.md
**Priority:** normal

Step 1a with a curation half: no statement says a base carrying a host beats a
hostless one, and the mechanism's other direction sits in `installation-boot`
where no root-404 query reaches it. `D-KNW-097` settles that it is a hint of its
own and what its two halves are; what it says is the reading. Establish against
`.checkouts/` how `SiteMatcher` orders the routes it hands `BestUrlMatcher`,
whether `getHostMatchScore()` and `sortMatchedRoutes()` score the same on `12.4`
and `13.4`, and what a site whose root page is deleted or hidden actually
answers — the message the feedback quotes is what separates this from a slug
problem.

`feedback/2026-08-18-074545` is the same gap by another mechanism and keeps its
own card; whoever judges it should read `D-KNW-097` first.
