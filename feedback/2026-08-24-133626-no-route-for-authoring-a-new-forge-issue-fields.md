---
date: 2026-08-24T13:36:26+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# No route for authoring a new Forge issue: fields and markup were guessed

## Observation

Task: after finishing a Core patch, write the title and description for the Forge issue that the patch's Resolves: trailer will point at.

I delivered a title, a metadata table (Tracker: Bug, Category: TypoScript, Target version: next-patchlevel, TYPO3 Version: 13/14/main, PHP Version, Complexity: easy, Is Regression: No, Sprint Focus) and a description body. Every one of those field names came out of my own recollection of the TYPO3 Core project's Redmine form. None of it was verified against anything. I did not call typo3_forge_lookup, and the reason is its name: it reads as "look up issue #N", the retrieval direction, not "how do I fill in a new one".

The concrete thing I hedged around: I did not know whether forge.typo3.org renders issue descriptions as Textile or as Markdown. Rather than find out, I wrapped every code block in raw <pre> tags and escaped the angle brackets in placeholders by hand (site-template-&lt;type&gt;-&lt;siteIdentifier&gt;), on the reasoning that <pre> survives both renderers. That is a workaround for a fact I could have been told in one line, and it produced a description that is uglier than either renderer needs.

Two other guesses shipped unverified. The Category value "TypoScript" — the handover artifact itself flagged this as open, between "TypoScript" and "Site Handling, Site Sets & Routing", and I picked one without being able to check the actual list of categories on the Core project. And "Target version: next-patchlevel", which I believe is the convention for a bugfix but did not confirm.

AGENTS.md in the checkout does document how to *read* Forge — curl the JSON API with the default user agent, browser-like agents hit the Anubis bot check, endpoints issues/<id>.json and issues.json?project_id=27. It says nothing about creating one, and I did not try to POST. So the read path is covered in the repo and the write path is covered nowhere I looked.

Worth noting for the record: the user came back with the issue number themselves, so nothing wrong shipped. But the description went out with three guessed values in it and a markup hedge, and I had no way to tell the user which parts I was sure of.

## Query

Never called. The request, in German: "ich brauche einen titel für das forge issue und eine beschreibung", followed two turns later by "hier ist das forge issue 110527". What I would have asked: "What fields does a new Bug on the TYPO3 Core project at forge.typo3.org require, which Category values exist, and does the description field render Textile or Markdown?"

## Suggestion

Cover the authoring direction, and say so in the name or the description of whatever covers it — typo3_forge_lookup's name promises retrieval and I did not think to try it for composition.

What the answer needs to carry, in the order I needed it: the field set of a new Bug on project 27, marking which are mandatory; the actual enumerated values for Category on that project (the list, not a description of it) so a caller can pick rather than guess; the convention for Target version on a bugfix; and one line on whether the description field is Textile or Markdown, with the code-block syntax that goes with it. That last one alone would have removed the <pre> hedge.

A worked skeleton for a bug report — symptom, steps to reproduce, cause, affected code — would also be worth having, but it is the field values and the markup that I could not derive and had to invent.
