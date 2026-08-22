---
id: D-DOC-035
title: What the prose costs is counted beside how long a sentence is
date: 2026-08-18
status: open
coveredBy:
  - ProseTest::whatTheCommentsCostIsMeasured
  - ProseTest::aCommentThatNamesAnEntryAndRetellsItAnywayIsReported
  - ProseTest::whatTheMarkupCostsIsNotCountedAsProse
---

# D-DOC-035 — What the prose costs is counted beside how long a sentence is

**`bin/cli prose:check` counts what the comments cost: the share of the PHP that
is comment, and every comment that names an entry and retells it anyway.**

The one counter this repository had measured length per sentence, which is
passed by writing two, and it read no file in `src/` at all.

## Evidence

- Measured on 2026-08-18: 13875 of 37622 non-blank lines below `src/` are
  comment, 37%. By group it runs from `Tool/` at 22.5% to `Search/` at 58.6%,
  and `src/Search/Text.php` carries 76 comment lines to 40 of code.
- 3384 comments below `src/`, of which 297 name a decision or a requirement. 172
  of those run past ten lines and hold 3006 lines between them. Below `tests/`
  the same count is 380 and 110.
- AGENTS.md already forbids what those are: "The reason lives in one place.
  Where a decision or a requirement carries it, the comment names the id instead
  of retelling it."
- Nothing read them. `prose:check` measured the markdown corpus and the connect
  payload, and `grep` for `T_COMMENT` across `tests/` and `src/Upkeep/` found
  nothing, so no check and no test counted a comment.
- The counter that existed rewards splitting: two sentences of twenty words pass
  where one of thirty-five does not, so the measure that was supposed to hold
  "length is a symptom" pushes the number of sentences up.
- `decisions/` holds 44289 lines in 375 entries, a mean of 996 words each,
  against 41569 lines below `src/`. The record corpus is larger than the code it
  decides about.

## Decided

- The comments are counted where the rule applies: `src/` and `tests/`. Both
  binaries are left out, because each locates an autoloader and hands its
  arguments to a class.
- What is reported is a share and not a count, because a count of something that
  grows is true on the day it is written.
- A retelling is a comment that names an entry and runs past ten lines. Where
  the line is drawn moves the count and not the finding: 216 such comments run
  past four lines, 205 past eight, 172 past ten.
- It reports and does not fail. A long comment naming an entry can be the right
  comment — it may rest on that decision while explaining something else — and
  only somebody reading the block can tell the two apart.
- Rejected: failing on the 240 comments the report finds today. That is a sweep
  of somebody reading each one, and it is queued as one.
- Rejected: a ceiling on the comment share per file. `Tool/` sits at 22.5%
  because its documentation is in the tool descriptions a client reads, and a
  ceiling would report that difference as a defect.

## Assumed

- What a retelling holds is in the entry it names, so cutting it loses no
  evidence. Nothing has read the 240 to check that.
- A session writes in the register of what it read. Every one of them reads
  AGENTS.md, `judging.rst` and `skills/base.md` before its first change, and
  AGENTS.md is second on this command's own list with 53 sentences past the
  measure — so shortening those moves what sessions write more than a rule does.
  Nothing measures that either.

## Wrong if

- The share falls while the entries grow to hold what was cut. That is the same
  prose one directory over, and it would show the measure moved the volume
  rather than removed it.
- The list sits at roughly 240 for months. Then it is read as a fact about this
  repository rather than as work, and a report nobody acts on is noise this
  command adds to.
- The comments on it turn out, on being read, to be right more often than not.
  Then the shape is wrong: what such a comment names is a cross-reference and
  not a retelling.

## Since then

**2026-08-18** — the three files the second **Assumed** names were cut, which is
the half of that measurement anybody here can carry out. By `wc -w`, AGENTS.md
went from 6798 words to 6149, `documentation/records/judging.rst` from 3978 to
3638 and `skills/base.md` from 2133 to 1971. On this command's own count the
same three went from 54 sentences over the measure to 46, from 37 to 32, and
from 22 to 22.

What came out is three things: the justification of a rule nobody would dispute,
a sentence restating the one above it, and a number measured somewhere else and
retold here — `D-FBK-020`'s call counts in AGENTS.md, `D-FBK-025`'s board in the
judging page, both of which now name the entry instead. Every rule stayed, and
in `skills/base.md` so did every sentence `SkillTest` quotes.

The two counts do not move together, which is the point. `skills/base.md` lost a
twelfth of its words and not one long sentence, because what came out of it was
whole sentences rather than the middle of any.

Whether that moves what a session writes is still unmeasured, and this commit is
the boundary the reading is against.

The sweep this entry queued was run on 2026-08-18, longest first, over the whole
list. The comments below `src/` were read and cut to what the code cannot say;
below `tests/` the same was done down to the tail. The list went from 240 to
166, the comment share of the PHP from 33.5% to 32.2%, and about 1,350 lines of
comment are gone. Nothing an entry does not carry was cut: what came out is the
account of the session, the counts and the sweeps, all of which the entry each
comment names already holds.

**The first assumption held and the third Wrong if did not fire.** Of the ones
read, none turned out to hold evidence its entry lacks — every retelling was one
— so cutting lost nothing. What such a comment is, once the retelling is out, is
what a reader editing that line has to know: the alternative that was rejected,
what breaks if somebody puts it back, and the id for the rest.

**The list has a floor, and the floor is the measure counting its own
structure.** A comment is measured from `/**` to `*/`, so an annotated docblock
spends seven of its ten lines on the delimiters, the summary line, one blank
line and a `@param` or `@return` — leaving three lines of prose. Two sentences
and an id do not fit in three lines. That is where most of the 166 sit: 133 of
them run to 13 lines or fewer, and reading one now finds a comment that was
already cut rather than one nobody has looked at. So the second **Wrong if** has
to be read against that floor: the number staying near 166 is the shape of the
count, and only a number staying near 240 is what it names.

**An id is matched wherever it appears, including where it is not a reason.**
The longest comment on the report was `Upkeep\Todo`'s class docblock at 59
lines, and the `D-CAT-001` it named was inside an example file name —
`todo/open/…-give-d-cat-001-a-digest-to-notice-markup-by.md`, an illustration of
what a todo is called. It was cut for another reason, the form it spelled out
being `todo/readme.md`'s, and the id went with the example. What a comment
quotes is not a comment resting on an entry, and the pattern cannot tell the two
apart.

The tail was read one at a time the same day: the 141 comments running to 11, 12
or 13 lines, each against the entry it names.

**The third Wrong if fires for that band.** 113 of the 141 are cross-references
and were left as they are. Such a comment names the id and then says what the
entry does not — which of its claims this line carries, what the alternative was
here, what the code does with it. The sweep before this one found retellings
because it went longest first, and length is where they were.

**What the other 28 carried is the entry's own account, and that is what came
off.** They are recognisable by shape rather than by wording: the paragraph a
decision opens with, a bullet of its **Evidence**, the **From** of a
requirement, the session whose failure produced it. `ToolContractTest` opened
with `D-ANS-017`'s bold sentence, `Process\CommandRunner` retold `R-COD-003`'s
`ddev` written onto the `PATH`, and `HintsTest` repeated `D-KNW-055`'s "nothing
out of 81 candidates". None of the 28 lost anything to the cut, which is the
first **Assumed** holding a second time.

**One comment on the list is there because its entry put it there.**
`D-FBK-042`'s **Decided** names the `Channel` docblock as one of the places the
two kinds of writing have to be named apart, so what reads as a retelling of the
statement is the statement being delivered. A comment naming an entry that names
the comment back is not something this pattern can see.

**Cutting the retelling does not reliably take a comment off the list.** Seven
of the 28 still run to 11 lines with nothing retold left in them, because an
annotated docblock spends the delimiters, the summary, a blank line and its
`@param` or `@return` before a sentence is written. The band cost 21 entries and
83 lines: the list went from 179 to 158 and the comment share of the PHP from
32.3% to 32.2%.

**So the count at ten lines has stopped naming work.** The band this reading
covered is read and right, and everything above it was cut by the sweep before.
The second **Wrong if** watched for the number sitting still; what happened is
the number falling to where it measures the shape of a docblock rather than what
is written in one. What the report should count instead is queued rather than
decided here.

**The register did not move.** The second **Assumed** was read on 2026-08-18,
against the claim rather than the merge: a session read the shortened files
where `bin/cli todo:claim` made its worktree after the cut, which puts the nine
claimed at 10:36 and 11:33 before it and the fourteen claimed between 12:10 and
12:58 after. The sweep's own session and this one are left out of both. In their
commit bodies the nine wrote 58 sentences at 19.7 words with 12 over the
measure, the fourteen 84 at 21.0 words with 24 over. In the entries they created
the same pair is 22.8 words and 23.0% against 23.6 and 26.4%. Both differences
run against the assumption and both are small.

**They are inside what a day of this corpus varies by.** The decision files
created on the thirteen days from 2026-07-31 that carry more than five of them
run from 19.7 to 25.3 words a sentence and from 8.6% to 33.9% over the measure.
Split at the cut's position in `main`, 2026-08-18 sits at 21.6 words and 19.2%
before it and at 22.0 and 22.3% after, mid-band on both sides.

**The comments went the other way, and cannot be read anyway.** Per line of PHP
the same sessions added, the nine before wrote 0.74 lines of comment and the
fourteen after 0.95, while the absolute count fell from 280 to 106 because two
of the nine wrote most of the code in the window. The sweep recorded above ran
in the same hours and cut the comments a session reads while editing the file it
is in, so nothing written that afternoon is attributable to the cut alone.

**What a session writes was already what the cut was reaching for.** `D-SKL-057`
and `D-SKL-058` are consecutive ids, the same work — a feedback judged into a
decision and its card — one on each side of the cut, 1027 words against 1180,
and the same entry to read. Both name their ids where the reason lives
elsewhere, which is the rule AGENTS.md was itself being held to that morning.

**One session wrote back a shape the cut had removed.** `c4f48e32` put "the work
such a todo carries is one entry's **Wrong if** gone back to, and `decisions/`
says only that somebody is sorting the pile" into `Upkeep\Todo`, and the second
clause verbatim into `TodoTest`. A sentence restating the one above it is one of
the three things that came out of AGENTS.md at 11:54, written again at 12:18 by
a session that had read the shortened file.

**So the assumption is not held.** The cut cost a session nothing and kept every
rule, so it stands on what it is; what it does not buy is shorter prose from the
sessions that read it, and shortening a file every session reads is not a way to
move what a session writes.

**2026-08-19** — the count is against a comment's prose lines rather than its
lines. What the markup costs does not count: the two delimiters, the blank lines
and the annotations. Everything else is a line somebody wrote, and ten of those
is more than saying which of an entry's claims this line carries.

**The floor is gone rather than worked around.** Raising the line to 13 or 14
was the other answer priced, and what it gives up is the retelling written
inside an annotated docblock — seven of the 28 the band above cut were exactly
that, still at 11 lines with nothing retold left in them. Leaving it was priced
too, and costs a list of 158 naming work nobody has to do.

**The summary line counts, against how this was priced in the card.** It is a
sentence somebody wrote, and taking it out would need a rule that holds for a
docblock and not for the other markup — one more concept for one line. The
annotations are the opposite case and run to the next blank line, because a
`@return` naming an array shape wraps over five of them.

**The numbers before this date are not the same measure.** The 240, the 166 and
the 158 above are comments naming an entry and running past ten lines, and this
one is comments writing past ten lines of prose. On the same corpus at this
commit the two are 164 and 11, of 748 comments that name an entry at all.

**The list starts holding no work, and that is what makes it readable.** Every
one of the 11 runs to 14 lines or more, so each was read by the sweep of
2026-08-18, longest first, and kept. What the second **Wrong if** watches from
here is the number rising off 11 — a comment somebody wrote a retelling into —
rather than a count sitting at the shape of a docblock.
