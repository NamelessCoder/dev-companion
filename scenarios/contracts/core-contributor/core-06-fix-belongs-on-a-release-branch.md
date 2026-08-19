# CORE-06 — The fix belongs on a release branch

**Environment:** `E-CORE`, checked out on a release branch · **Contract:**
`open` — `R-AUD-004`
**Held by:** `VersionsTest::aStatedVersionWinsOverTheInstallationBeingRead`,
`CatalogTest::aComponentNotVerifiedOnTheTargetIsDeclinedRatherThanHandedOver`;
that a release-branch patch changes trailer and refspec is not guarded
(`R-AUD-004` open)

**Read 2026-08-19:** `R-AUD-004` is `held` today, so the `open` beside it above
is stale, and the push itself is answered by the corpus `R-KNW-057` holds. What
stays unguarded is that a session changes trailer and refspec when the patch
goes to the branch directly.

> This bug only exists on 13.4, on main the code was rewritten and the problem is
> gone. Prepare the patch for 13.4 and tell me what is different about pushing it
> there.

**What the agent needs from this server**

- That a patch which cannot go to `main` is pushed to the release branch
  directly, and what that changes about the release trailer and the refspec.
- Whether the conventions and any catalog answer it hands over hold on 13.4 at
  all.

**What has to come out of it**

- The release trailer names the branch the patch targets, and the push goes to
  that branch's refspec.
- Where an answer was taken from a pinned revision, the answer says so, and
  where the server cannot know whether something holds on 13.4, it says that
  rather than implying it does.

**How it fails**

- Anything stated for "TYPO3" that is really only true on the revision the
  catalogs were taken from, with no word about the branch (`R-AUD-004`).
- The normal main-first rule applied to a bug that does not exist on main.
