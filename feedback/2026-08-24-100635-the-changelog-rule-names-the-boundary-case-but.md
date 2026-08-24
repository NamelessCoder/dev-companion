---
date: 2026-08-24T10:06:35+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_rule_lookup, typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# The changelog rule names the boundary case but leaves the caller to judge it alone

## Observation

Task: decide what Gerrit change 95375 owes, first as a reviewer and then as its author after reworking it into a change that alters what a documented configuration option means.

typo3_rule_lookup(query="changelog entry review readiness") answered the easy half exactly right and saved me from a false finding: "A casual bug fix owes no entry, because its commit message carries the information. Demanding one of a BUGFIX that removes nothing public is a review defect of its own."

Then it named the boundary and stopped: "A bug fix that makes a documented sentence false is not the casual one that owes no entry. The casual bug fix is the one nothing outside the code has to learn about, and Important is the type that carries the rest on a release line the other three may not reach."

My reworked patch sits exactly on that boundary and I had to judge it myself, with no confidence. What it does: the "Create new form" wizard's Blank mode no longer picks any entry from formManager.selectablePrototypesConfiguration.*.newFormTemplates. Before, blank mode used whichever entry was configured first, so an integrator who put their own blank template first could steer it. Now the server always builds from the blank form EXT:form ships. That is a capability integrators had — accidentally, undocumented, but real — and it is gone. I added a paragraph to typo3/sysext/form/Documentation/I/Config/formManager/Index.rst saying so, and I concluded no changelog entry is owed and that Releases: main, 14.3 stands.

I am not confident in that. The rule gives me two poles — "nothing outside the code has to learn about" versus "makes a documented sentence false" — and my case is a third thing: nothing documented becomes false, but behaviour an integrator could rely on changes, and the manual has to gain a sentence it did not have. There is no test in the rule for that, and the decision determines both the changelog obligation and whether the change may reach a maintained release line at all.

The related hint id breaking-without-a-moved-member was named in the alsoInHints field of that answer, and I did not fetch it. That is on me — but it was listed as a hint id under alsoInHints rather than as the answer to the question the section had just raised, and I read past it.

typo3_commit_message_guide behaved well here by contrast: it reported breaking-not-assessed on my first call, said plainly that it cannot see the diff, and told me what makes a change breaking. I acted on it and passed isBreaking=false and isDeprecation=false on the later calls after enumerating what the diff removes. A tool reporting its own blind spot is what made that work.

## Query

typo3_rule_lookup(query="changelog entry review readiness", targetVersion="15") — see the "Changelog Files" section from core/contribution/commit-messages and the "Documentation" section from core/contribution/rules. The case to test it against: a BUGFIX that removes no public member, breaks no signature, but changes which configured value a backend wizard uses and requires a new paragraph in a system extension's own Documentation/.

## Suggestion

The section reaches the boundary and then hands the caller a judgement with no procedure. What would settle it is a short decision test in the same section, of the shape: does any documented sentence become false (yes -> entry), does an integrator lose a behaviour they could configure (yes -> Important, and it decides the release line), does the manual need new text (yes -> the patch carries it, and ask again whether that text describes a change or only fills a gap), otherwise none.

The alsoInHints pointer should be promoted where the section itself raised the question the hint answers. The Changelog Files text already says "Breaking reaches past a moved PHP member... which of those the core files as breaking, and where the boundary against Important runs, is typo3_hint_lookup with the id breaking-without-a-moved-member" — that sentence is in the body and I still read past it, because it arrived as one bullet among ten and my case did not look like a breaking change at all. Saying it once at the end, as the next call to make when the change alters behaviour without moving a member, would have caught me.

Keep typo3_commit_message_guide's breaking-not-assessed check exactly as it is. It is the clearest example in this server of a tool naming what it cannot know, and it changed what I did.
