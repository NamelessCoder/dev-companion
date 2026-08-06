---
date: 2026-08-05T22:11:04+00:00
category: tool-gap
status: closed
closed: 2026-08-06
model: claude-opus-5[1m]
tool: typo3_configuration_lookup, typo3_hint_lookup
directory: /home/benji/projects/typo3-cms
---

# Task: verify Forge #85456, "columnsOverrides not possible for type group", against a 15.0.0-dev c...

## Observation

Task: verify Forge #85456, "columnsOverrides not possible for type group", against a 15.0.0-dev core checkout. The report's stated cause was an ordering claim — "TcaColumnsOverrides is called after TcaGroup" — so settling the issue meant settling that order.

Nothing on the server answers it. typo3_configuration_lookup returns the raw registry under SYS/formEngine/formDataGroup. The formdata-providers hint from typo3_hint_lookup is exactly right about why that is not the answer — "Each provider maps to an array with 'depends' and 'before' ... That graph is what orders the run, not the order the entries are written in" — but having said so it leaves the reader holding a dependency graph and no way to evaluate it. Reading it by hand was not viable: the tcaDatabaseRecord group has 61 providers, and the two I cared about sit 130 lines apart in DefaultConfiguration.php with no edge between them.

What I did instead: wrote a throwaway PHP file into the repository root, required the autoloader and DefaultConfiguration.php, and called DependencyOrderingService::orderByDependencies($list, 'before', 'depends') myself, then ran it through `ddev exec`. It answered TcaColumnsOverrides at index 21 and TcaGroup at 41, which settled the issue — the report's cause is stale on this branch. Two costs beside the detour: I had to write a temporary file into the user's checkout and remember to delete it, because the quoting did not survive `ddev exec php -r`.

I would do the same again next session, and would rather not.

## Query

typo3_configuration_lookup with path "SYS/formEngine/formDataGroup/tcaDatabaseRecord", and typo3_hint_lookup with paths ["typo3/sysext/backend/Classes/Form/FormDataProvider/TcaInputPlaceholders.php"] which returned the formdata-providers hint. Underlying question: does TcaColumnsOverrides run before or after TcaGroup in the tcaDatabaseRecord group?

## Suggestion

Have typo3_configuration_lookup answer with the resolved order when the path names a form data group — as an extra field beside the raw map, or behind an argument, or as its own tool. The registry under SYS/formEngine/formDataGroup is one of the few configuration values whose assembled shape is not what anybody wants; the answer is the topological order it implies, and the server already has both the shape and the resolver the core uses. Concretely, for SYS/formEngine/formDataGroup/tcaDatabaseRecord, return the providers in execution order with their index and their declared depends/before. That turns "does A run before B" from a script into a read, and it is the question every "this provider sees stale data" bug reduces to — which is what the formdata-providers hint says itself before stopping. Same for the other groups: tcaSelectTreeAjaxFieldData, flexFormSegment, siteConfiguration.
