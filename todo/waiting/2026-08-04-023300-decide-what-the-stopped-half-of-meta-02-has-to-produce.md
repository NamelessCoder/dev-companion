# Decide what the stopped half of `META-02` has to produce

**Serves:** META-02
**Priority:** normal
**Waiting on:** what does the `E-STOPPED` run of `META-02` have to produce, now
    that both lookups its prompt reaches answer from the package files where the
    console cannot? Rewriting criteria 1, 2, 4 and 6 to what the server promises
    there — an answer that names the weaker source and what it leaves out —
    keeps the case and gives up the "found but not running" reason it was
    written for. Giving the case a second prompt, one whose lookups have no file
    fallback, keeps that reason and makes the case two prompts, which is the
    shape `scenarios/readme.md` says one file may not have.

The `E-NONE` half ran on 2026-08-04 and came out as the case asks; what it
settled is on `D-ANS-005`. The other half never reached the state it names.
`typo3_icon_lookup` and `typo3_label_lookup` both fall back to the packages' own
files (`R-ANS-008`), so a stopped project answers the prompt rather than
reporting that it could not be asked, and the session driven in
`.environments/e-site-main` answered with icons and labels and said in its own
words that they came from the files because the console refused to boot. It
named neither `ddev start` nor the two settings: the caveat that carries both
sits in `typo3_server_scope` and in an `unsupported` diagnosis, and a lookup
that answered from the files reaches neither. Criterion 6 has nothing left to
observe once the first four go, because there is no negative to change.
