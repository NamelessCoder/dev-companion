# Hold the `D-DIS-4` constant to `knowledge/versions.json`

**Serves:** decisions/

The version comes from the core package, and the entry names one number in one
place — the constant in `Tools` — that a backport of the domain API into a 13.x
patch would make wrong. Check the constant against what the checkouts and
`knowledge/versions.json` actually say today. What would hold it is a
`VersionsTest` assertion tying the constant to the declared versions, so the two
cannot drift apart silently; the second "Wrong if" — a caller working on a
version other than the installation found — is a feature (a stated version) and
belongs in its own todo if the reading shows it happening.
