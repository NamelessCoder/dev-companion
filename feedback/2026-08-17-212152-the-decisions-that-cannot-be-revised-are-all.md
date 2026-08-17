---
date: 2026-08-17T21:21:52+00:00
category: tool-gap
status: open
model: claude-opus-5
tool: typo3_project_describe, typo3_documentation_lookup, typo3-development-installation
directory: /home/benji/projects/site-demo
---

# the decisions that cannot be revised are all made before an installation exists, which is when al...

## Observation

Task: build a TYPO3 v14 demo site as a sitepackage plus a distribution extension carrying the content, from an empty directory, on TYPO3 14.3.6. I have named this in the suggestion field of two other feedbacks and it belongs on its own, because as a clause inside somebody else's finding it will not be worked off.

Within the first fifteen minutes, before anything was installed, I wrote three files: the root composer.json (the package set, the PHP constraint, the extra.typo3/cms keys), .ddev/config.yaml (php_version, docroot, database), and .gitignore. All three are the ones nobody revisits — the container is built from the second, the third is wrong forever once the first commit lands, and the PHP floor in the first becomes a support claim. Two of the reviewer's ten findings and one of my own filed gaps trace directly to those minutes.

At that point typo3_project_describe correctly answers no-installation and stops, which is right — it reads an installation and there is none. What is missing is anywhere else to go. The version-bound hints did work and I used them heavily; what they do not carry is the installation-shaped facts: which PHP versions 14.3 supports, what the installer writes into the tree, which extra keys the Composer installer reads on this line.

One tool did work and is worth saying so: typo3_system_extension_lookup answered with no installation present, gave me all 36 system extensions for v14, and caught that typo3/cms-recordlist does not exist there — which would have failed my first composer require. That is exactly the shape the pre-install moment needs, and it proves the surface is possible rather than merely desirable.

Two places show the circularity sharply. typo3-development-installation step 1 says to establish the extra block by asking typo3_documentation_lookup "and read the installed installer package where the documentation is thinner than the question" — the installed installer package is what you are trying to install. And I did ask documentation_lookup, twice across the session; both times it returned a list of page titles I never read into with `page`, and neither changed a decision. The System Requirements page it surfaced for 14.3 defers concrete versions to an external page in its own summary.

So the practical shape of the session was: guess three files from memory, install, and then discover from the answers that arrive afterwards which guesses were wrong — at a point where changing them means rebuilding the container or rewriting history.

## Query

Start in an empty directory. typo3_project_describe answers no-installation. Now write the root composer.json, .ddev/config.yaml and .gitignore — and see which of those choices the server can inform.

## Suggestion

Treat "before the installation exists" as a supported state rather than an absence, since it is where the irreversible choices are made. Three facts would cover most of it and none needs an installation to answer: the PHP range a target version supports, the set of paths a Composer installation generates (which I filed separately for the ignore-file case), and the extra.typo3/cms keys that line honours. typo3_system_extension_lookup already demonstrates the pattern — version-keyed, installation-free, and it caught a real error for me. Where typo3_project_describe answers no-installation, that answer is also the natural place to say what can still be asked; right now it lists the paths it searched, which tells the caller what failed rather than what to do next.
