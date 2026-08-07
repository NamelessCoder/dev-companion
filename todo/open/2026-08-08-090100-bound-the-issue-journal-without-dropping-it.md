# Bound the issue journal without dropping it

**Serves:** feedback/2026-08-07-231213-typo3-forge-lookup-cannot-read-an-issue-without.md, feedback/2026-08-07-233524-typo3-forge-lookup-returning-notes-and.md
**Priority:** normal

`typo3_forge_lookup` reads an issue in one size. Selecting the first real bug
out of thirty candidates is per-issue judgement whose evidence is in the
comments — 14858 is a Bug until the last note calls it a feature — so the honest
way to do it is to read journals until one survives, and the reporting session
says it could not have afforded that across ten. Measured on 2026-08-08: 14858
answers 4090 characters of which 2573 are the journal, and 8 of its 15 notes are
Gerrit Code Review patch-set pings; on 15984, 3 of 15.

Read the counterweight before designing this. The same session filed the journal
as the thing that saved it three separate times, and `D-ANS-064` records why the
answer is made legible rather than smaller — so the bound is a parameter and
never a default, and a caller reading one issue keeps exactly what it has today.
Of the three shapes offered, drop the bot notes first: it takes half the volume
off 14858 and removes nothing a reader was going to use. The authors seen are
"Gerrit Code Review" and "Mr. Hudson", which is a list and not a rule — say in
the answer how many notes were filtered, so a bot nobody named is visible as a
count that did not move.

**A second session put numbers on the counterweight, and they rule out the
obvious bound.** On 2026-08-07 a triage of the same backlog reports that the
decisive note on 14858 was the sixteenth of sixteen and the decisive note on
15984 the twelfth of sixteen — the first turning a twenty-year-old feature
request away from being reported as a verified defect, the second carrying the
2012 revert reason. Neither is near the top. So "the most recent N" is not the
shape, and neither is sampling: what may be dropped is what a reader was never
going to use, which is why the bot filter is first and why the count of what was
filtered is said out loud.
