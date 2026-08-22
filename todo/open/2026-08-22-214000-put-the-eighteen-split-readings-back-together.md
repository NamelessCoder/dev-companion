# Put the eighteen split readings back together

**Serves:** D-DOC-041
**Priority:** normal
**Run:** git show e63c2577 --stat

Eighteen entries carry a reading in two places. `e63c2577` wrote every dated
label as the section it is, and where a label stood inside `## Wrong if` or
`## Decided` it moved that heading to the foot with the reading's first
paragraph alone — so the rest of each reading stands where it was, under bullets
it has nothing to do with, and the entry's own history counts as decision in
what `bin/cli decisions:check` measures.

`D-AUD-007` is the worked case: five of its six readings were split that way and
sixty lines of history stood in **Wrong if**, which is why it entered the
outgrown report the moment it was put back together. `D-KNW-049` is the other
shape — two readings split, and the halves left behind attached to a reading
they have nothing to do with, in an order that reads backwards.

Take one entry at a time. `git show e63c2577^:<path>` is the file before the
conversion and says which paragraph belonged to which label, so the reading is
which heading each orphan goes under and whether the dated sections then stand
in date order. Judge the sections while the entry is open, the way the thirteen
were, and finishing one is a commit of its own.

The eighteen are `D-ANS-052`, `D-ANS-053`, `D-ANS-059`, `D-ANS-060`,
`D-ANS-061`, `D-ANS-062`, `D-CAT-004`, `D-FBK-039`, `D-GUI-010`, `D-KNW-060`,
`D-KNW-061`, `D-KNW-063`, `D-KNW-086`, `D-KNW-088`, `D-SKL-017`, `D-SKL-018`,
`D-SKL-038` and `D-SKL-053`. Each was read the same way: a file the conversion
touched whose section bullets are followed by prose. `D-DOC-023` and `D-FBK-042`
read as that too and are not this — the prose after their bullets is nobody's
conversion.
