# Ask whether the package may be published under the `typo3/` vendor

**Serves:** decisions/
**Priority:** high
**Waiting on:** whoever administers the `typo3/` Composer vendor saying whether
    this package may be published there. Nothing in this checkout can answer it,
    and no reading of the code would make the answer more likely to be yes.

`D-AUD-008` renamed the package to `typo3/dev-companion` and the rename is
carried through: the binary, the namespace, the environment variables and the
state directory all moved on 2026-08-06. The name half of that is settled by
what is in the repository. The vendor half is not, and it is the half that
carries a claim about who this belongs to.

The argument to put is the one the decision rests on. `typo3/` without `cms-` is
where the tooling sits that is official and is not a system extension —
`typo3/tailor`, `typo3/coding-standards`, `typo3/testing-framework` — and this
is that kind of package. The old name `typo3/cms-mcp` claimed the other thing,
because `cms-` is the prefix the core's system extensions are published under.

**Ask before the first release, not after it.** Nothing breaks while the package
is unpublished: the vendor appears in `composer.json` and in the install
instructions and nowhere a caller depends on. Once it is on Packagist the answer
costs a second rename, and that is the one people notice — which is the first
entry in the **Wrong if** of `D-AUD-008`.

If the answer is no, only `composer.json` changes. `dev-companion` stays the
name, and the vendor becomes a personal or organisational one; the namespace
`TYPO3\DevCompanion\` is worth a second look in that case, because it was chosen
to sit beside `TYPO3\Tailor\` and it would then be sitting beside nothing.
