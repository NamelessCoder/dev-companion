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

That last sentence was written against `.environments/e-site-main` alone, and
that environment is out of the case: it declares no platform and its
`platform_check.php` wants `>= 80500`, so host PHP 8.3 cannot run its console
and the resolution fails rather than answering weakly. The three released
environments are not out of it — `e-site-13.4` pins `8.2.0`, `14.3` the same,
`12.4` pins `8.1.1`, and host PHP satisfies all three, so a stopped project
there resolves through host PHP and carries a caveat. Since `4b43734` that
caveated resolution is no longer remembered, and `typo3_server_scope` was
measured changing across a `ddev start` inside one process — 0.000s `via=php`
with a stale caveat before, 0.503s `via=ddev` php=8.4 with none after. So
something does change in one session in those three, which is what criterion 6
asks about, and the answer to this todo cannot be read off `e-site-main`.

What that leaves open, and what nobody has run: whether the change reaches the
**prompt's own two lookups** rather than only `typo3_server_scope`. In these
three a stopped project's console runs, so `typo3_icon_lookup` and
`typo3_label_lookup` answer from the console rather than from the package files
`R-ANS-008` falls back to, and what the `ddev start` alters may be the caveat
and the source rather than the icons and labels themselves. Driving the prompt
against `.environments/e-site-13.4` stopped, in one session, across a real
`ddev start` is what says which — and it is a different run from the one that
produced the paragraph above.
