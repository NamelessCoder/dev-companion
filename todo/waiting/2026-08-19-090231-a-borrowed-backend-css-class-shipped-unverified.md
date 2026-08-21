# A borrowed backend CSS class shipped unverified: component_lookup fitted and stayed shut

**Serves:** feedback/2026-08-19-090231-a-borrowed-backend-css-class-shipped-unverified.md
**Priority:** normal
**Waiting on:** may a catalog entry answer for one class below the version the
    entry is bound on? `table-fit` is written in
    `Build/Sources/Sass/component/_table.scss` on `12.4`, `13.4`, `14.3` and
    `main`, and `typo3_component_lookup` withholds it for a caller on v12
    because `--typo3-table-border-radius`, one of the eleven custom properties
    in the same entry, arrived in v14. Withholding that property is what
    `D-CAT-001` is for, and a per-field range was rejected there as four ranges
    in an answer whose reader wants one question answered. Withholding the class
    costs the caller the answer this tool exists to give, which is what
    `feedback/2026-08-19-090231` is.

Three options, cheapest first. Leave the design and say the reason in the
    miss, which today names the Sass path to read and not why the entry was
    held back — cheap, and the caller still reads the stylesheet by hand. Carry
    a second derived range for the class list alone, beside the entry's own —
    one more number per entry, derived by the same command, and the class-shaped
    query is answered where the paste-shaped one is still withheld. Answer a
    class-shaped query out of the Sass sources the entry already names, which
    moves the catalog from an index to a reader of four checkouts and is the
    expensive one.

    Recommended: the second. The class list is what a borrowed class is asked
    against, it derives from what `describesTheSame()` already reads, and it
    leaves the paste contract exactly where `D-CAT-001` put it.

Judged 5 on 2026-08-21 — everything worked as designed, and the design is the
price; priority `normal` because the reading behind it is done rather than
because a second session reported it. `D-CAT-001` carries what the re-run and
the four checkouts said, and `D-SKL-067` carries what it means for the workflow
that would have told this session to make the call at all; nothing here is open
but the question above.
