---
date: 2026-08-21T07:40:10+00:00
category: tool-gap
status: closed
closed: 2026-08-21
model: claude-opus-5[1m]
tool: typo3_gerrit_lookup
---

# a Gerrit change is read alone while the feature it belongs to is a relation chain

## Observation

Task: compare this server against another TYPO3 MCP server and write a detailed document, verifying every claim against the two working trees and against the review server.

typo3_gerrit_lookup with change=91563 answered well and in one call: subject, branch, current patch set with its commit, the fetch ref, and the labels. Asked again with messages="people" it gave the upload history and the copy conditions that dropped votes. Nothing about that reading had to be repeated.

What it does not answer is the relation chain. 91563 is [WIP][FEATURE] Introduce Action API, and it is one change in a stack of fifteen: a JSON SchemaBuilder underneath it (93064), an OpenAPI surface, an MCP server and an AI tool provider built on it (91666, 92224, 92223), and PageTree, dashboard and resource endpoints being migrated onto it (92191, 92322, 93599). Read alone, 91563 says a feature exists. Read as a chain, it says what the feature consists of, which parts are merged, which are abandoned, and how far it has got.

The tool does answer the changes sharing a Change-Id, and that is a different set: those are one change on several branches, which is what a backport keeps. A relation chain is a sequence of different changes built on one another. The two answer different questions and only one of them is available.

I fell back to an unauthenticated GET on /changes/91563/revisions/current/related, which returned all fifteen with number, status and subject in one response. That one call is what made the feature legible; without it I had the head of a stack and no way to see it was a stack.

## Query

typo3_gerrit_lookup with change=91563; then a fallback GET to /changes/91563/revisions/current/related

## Suggestion

Carry the relation chain in the answer when typo3_gerrit_lookup is given a change handle, beside the Change-Id siblings it already returns, and name which is which - a stack of changes built on one another against one change on several branches. Number, status and subject per entry is enough to see the shape. The source is /changes/{id}/revisions/current/related on the same anonymous REST API the tool already reads, so it costs no credential and no extra host. The question it answers is "is somebody already building this", where the head change alone regularly understates a body of work by an order of magnitude.
