---
date: 2026-08-05T03:39:24+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_rule_lookup, typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# Task was to write a core bugfix patch for Forge #88556 against main (15.0.0-dev) and produce its ...

## Observation

Task was to write a core bugfix patch for Forge #88556 against main (15.0.0-dev) and produce its commit message.

Nothing on the server says which release branches are currently maintained, so the value of the `Releases:` trailer — a required part of every core commit message — has no source here.

typo3_rule_lookup answered the changelog half precisely and usefully ("A casual bug fix owes no entry, because its commit message carries the information", plus where entries live and that a backport goes into the <lts>.x directory of the oldest branch it reaches). But "the oldest branch it reaches" is exactly the fact it does not supply.

typo3_commit_message_guide has a `releases` parameter and its description says "Left out, the draft carries a RELEASE_TARGET placeholder and the checks ask for it — the branches a change is released on are not guessed." That is the right stance, but it means the tool asks the caller for a fact the server does not offer anywhere, and it validated nothing about what I passed: "main, 14.3, 13.4" came back with "No commit message readiness issues found". It would have accepted a long-dead branch just as happily.

I established it from the checkout instead, by counting trailers on recent commits:
  git log -40 --format='%s%n%b' origin/main | grep -i '^Releases:' | sort | uniq -c | sort -rn
  -> 19x "Releases: main, 14.3"   11x "Releases: main"   10x "Releases: main, 14.3, 13.4"
and then confirmed the defect actually exists on the other two by reading the file on those branches (git show origin/14.3:typo3/sysext/core/Classes/Html/RteHtmlParser.php and the same for origin/13.4 both still carry the `explode(LF, $value)` line the patch replaces).

That worked, but it is inference from a sample of 40 commits, and it is the kind of fact that goes stale in exactly the way a knowledge server exists to prevent. `git branch -r` lists everything back to TYPO3_3-6, so the checkout does not answer it either — a session with less caution would pick the branch list off the remote and be wrong.

## Query

typo3_rule_lookup {query: "bugfix changelog entry requirements and target branch"} then typo3_commit_message_guide {workflow: "core", changeType: "BUGFIX", summary: "...", issue: "88556", releases: ["main","14.3","13.4"], isBreaking: false, isDeprecation: false}. Neither answered which branches a bugfix should be released on today.

## Suggestion

Hold the currently maintained release lines and what each is (main / dev, current stable sprint line, LTS, ELTS, security-only, end of life) as a lookup, and state the default Releases: trailer for each change type against them — a bugfix that applies to LTS, a bugfix that only applies to main, a feature, a deprecation. Then have typo3_commit_message_guide validate the `releases` argument against that list instead of accepting any string: an unmaintained or non-existent branch in the trailer should be a check finding. Worth pairing with the reminder that the trailer is a claim the author has to have verified on those branches, which for this patch meant reading the changed file on origin/14.3 and origin/13.4.
