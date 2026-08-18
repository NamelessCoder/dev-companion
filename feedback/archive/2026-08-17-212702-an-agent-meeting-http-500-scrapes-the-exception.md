---
date: 2026-08-17T21:27:02+00:00
category: missing-knowledge
status: closed
closed: 2026-08-18
model: claude-opus-5
tool: typo3_hint_lookup, typo3-development-installation
directory: /home/benji/projects/site-demo
---

# an agent meeting HTTP 500 scrapes the exception out of 24KB of HTML because nothing names the log...

## Observation

Task: build a TYPO3 v14 demo site as a sitepackage plus a distribution extension carrying the content, on TYPO3 14.3.6 with DDEV. Filing this at the user's insistence after I dismissed it as my own clumsiness; his argument is that the next agent will do exactly the same, and having traced it, he is right.

Four separate times the site answered HTTP 500 and I had no browser. Four times I fetched the page with curl, got roughly 24,000 characters of HTML and inline CSS wrapping one line of message, and re-invented an extraction — grep, then sed, then a Python regex stripping style and script tags — because the exception text is displayed and nothing told me it is also written down. About ten round trips across the session, and each of those HTML bodies passed through my context.

var/log/typo3_*.log is the standard sink and would have answered in one call. Nothing in the two skills or in any hint I read names it. typo3-development-installation's proving step says the site must answer on both sides and stops there; it does not say where to look when it does not. That is the whole of the finding as an agent experiences it: the error is presented in the medium the agent does not have, and the medium it does have is unmentioned.

The sharper half is what I found while checking whether a hint already covered it. installation-boot names exception 1396795884 verbatim — VerifyHostHeader against SYS/trustedHostsPattern — which is precisely the failure that cost me six round trips and two diagnostic detours. It also says database:updateschema is not the core's command, which I established by running typo3 list, and it explains the NullSite behaviour behind a base that matches no host.

I never called it, and the reason is structural rather than careless. typo3-development-installation forks explicitly: a repository that already declares an installation is booted from what it declares, one that declares none has an installation created for it. I was on the create branch, so a hint titled "Booting the Installation a Project Repository Declares" read as the other branch's material and I ruled it out from its title. What I did not notice is that the fork reverses: once I had torn everything down to prove the clean-clone sequence, I was booting a repository that declares an installation, which is exactly that hint's case. Nothing re-raised it at that point, and I hit 1396795884 again on that very rebuild.

So the exception numbers that cost the most in this session were sitting in a hint I had eliminated by title, on the strength of a branch decision made forty minutes earlier that had since flipped.

## Query

typo3-development-installation, create-branch, on an empty directory. Get the site to answer 500, then watch how the error text is obtained. Compare against typo3_hint_lookup id=installation-boot, which names the exception.

## Suggestion

Name the log where the workflow says the site must answer: var/log/typo3_*.log holds the exception, one call instead of scraping the rendered page, and an agent working without a browser has no other way to know. Then reconsider what installation-boot is indexed under — its trusted-hosts, NullSite-base and database:updateschema content is diagnostic knowledge that applies to any installation returning 500 or 404, not only to one being booted from a declaration, and its title hides that from anyone the workflow sent down the create branch. Either surface those symptoms where both branches can see them, or have the create branch name installation-boot at the point where it asks the caller to prove the clean-clone sequence, since that is where the create branch turns into the boot case.
