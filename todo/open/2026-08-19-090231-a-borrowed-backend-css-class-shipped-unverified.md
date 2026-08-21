# Carry a second derived range for the class list

**Serves:** feedback/2026-08-19-090231-a-borrowed-backend-css-class-shipped-unverified.md
**Priority:** normal

Answered by the maintainer on 2026-08-21: a catalog entry may answer a
class-shaped query below the version the entry is bound on, by carrying a second
derived range for the class list alone, beside the entry's own. The paste
contract stays where `D-CAT-001` put it — a paste-shaped query on an entry bound
above the caller's major is still withheld.

The question was whether `table-fit` may be answered for a caller on v12. It is
written in `Build/Sources/Sass/component/_table.scss` on `12.4`, `13.4`, `14.3`
and `main`, and `typo3_component_lookup` withholds the whole entry because
`--typo3-table-border-radius`, one of the eleven custom properties in the same
entry, arrived in v14. The two rejected options are in the history of this file:
leaving the design and only saying the reason in the miss, and answering out of
the Sass sources, which would make the catalog a reader of four checkouts.

The range derives from what `describesTheSame()` already reads, so the same
command that derives the entry's range derives this one. What still has to be
settled in the work: which answer shape says that a class is covered while the
entry is not, so a caller cannot read the one as the other.

Judged 5 on 2026-08-21 — everything worked as designed, and the design is the
price; priority `normal` because the reading behind it is done rather than
because a second session reported it. `D-CAT-001` carries what the re-run and
the four checkouts said, and `D-SKL-067` carries what it means for the workflow
that would have told this session to make the call at all.
