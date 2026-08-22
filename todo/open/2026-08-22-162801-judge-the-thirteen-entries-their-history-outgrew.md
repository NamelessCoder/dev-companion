# Judge the thirteen entries their history outgrew

**Serves:** D-DOC-041
**Priority:** normal
**Run:** bin/cli decisions:check

Take one entry at a time and ask of each dated section whether it established
something or only counted. `D-FBK-018` is the worked reading and the largest:
four of its nineteen sections open "the reading held a fourth time", "a fifth
time", "a sixth time" and add nothing a reader needs twice, while the rest fire
a **Wrong if** or add a clause. What only counted becomes a line in one section
naming the feedback it read; what established something stays, and where it
overtook a bullet above it, that bullet is struck — the convention 4 entries of
441 use, and `D-SKL-001` carries ten readings over 57 lines of decision without
one. Where a reading settled something of its own rather than confirming this
entry, it is a new entry and the old one points at it, which is what
[documentation/records/writing-a-decision.rst](../../documentation/records/writing-a-decision.rst)
already prescribes for a statement that stopped describing the server. The
thirteen are the entries `bin/cli decisions:check` reports with four or more
dated sections, longest first, and finishing one is a commit of its own — this
is thirteen readings rather than one sweep, and the report is what says how far
it has got.
