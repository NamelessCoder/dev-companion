# Commit in a project repository and see whether `D-GUI-002` holds

**Serves:** decisions/
**Priority:** normal
**Run:** bin/cli scenarios:contract EXT-03
**Branch:** todo/commit-in-a-project-repository-and-see-whether-d-gui-2
**Claimed:** 2026-08-04

Both halves of what this waited on were answered on 2026-08-03. A session may
change code and commit in one of the `E-EXT` checkouts, and this server may not
do either itself — it says what is to be done, and it validates. The run is
recorded as a feedback rather than as a fourth forward review, so `D-EVI-001`
and `D-EVI-003` stay as they are. Drive one session in
`/home/benji/projects/syntax`, where the argument was already measured over
stdio; `/home/benji/projects/news` is somebody else's checkout and plays a
scenario a commit there would change. Start it the way
[documentation/driving-a-session.md](../../documentation/driving-a-session.md)
says, and publish the skills from here into that checkout first, because the
copies over there go stale without saying so. Give it a task in the shape of
`EXT-03`: a real defect in that extension, reproduced, fixed and committed. Its
own prompt is not pasted, because it names a list plugin that checkout has not
got and because a contract case is read rather than run forward. Then read the
transcript for one thing — whether `typo3_commit_message_guide` was called with
`workflow: "project"` — and the landed message for the `Releases:` line and the
Forge trailer. Record which route reached the commit step. Of the nine published
skills only the two core ones name that tool, so a session that never arrives
there measured that hole rather than the argument's default. Write
what came back into `feedback/`, note what the run left the checkout in on
[todo/reference/which-checkout-plays-which-environment.md](../reference/which-checkout-plays-which-environment.md),
and answer the first half of the `D-GUI-002` **Wrong if** from it.
