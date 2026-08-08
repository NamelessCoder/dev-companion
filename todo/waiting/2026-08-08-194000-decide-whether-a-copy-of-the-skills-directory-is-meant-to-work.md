# Decide whether a copy of the skills directory is meant to work

**Serves:** R-SKL-008
**Priority:** normal
**Waiting on:** whoever owns this package saying which of three a tree install
    of `skills/` is — meant to work, meant to fail loudly, or nothing this
    repository supports. Nothing in the checkout can answer it: all three are
    consistent with what the code does today, and each costs a different change.

Every `SKILL.md` now carries a `compatibility` line saying which server it needs
and how it is installed, so a copy taken out of this repository says what is
missing. It does not put `references/base.md` there. `Installer::BASE` writes
that file at publication, one copy per skill, and a copy that never went through
the installer opens on a link to nothing.

Making the copy work means committing one `references/base.md` per skill, which
two things in `Installer` currently refuse: `digest()` hashes `skills/base.md`
once because it is the only copy here, and `publishSkill()` writes the copy
itself, so a committed one would be overwritten on every publication and would
go stale in the repository between them. Making it fail loudly means the
opposite — the link stays dead and something in the tree says so louder than one
front matter line. Supporting nothing means saying so where a copier looks,
which is `readme.md` rather than a skill.

Put the three with what each costs, and write the answer into `decisions/`,
because it decides what a later session may commit under `skills/`.
