# What the server answers, and from where

Almost everything comes from the bundled `knowledge/` files. The rest is owned
by the installation the agent is working in, because no bundled answer could be
right for it.

- [versions.md](versions.md) — writing a statement that holds for some TYPO3
  versions and not others, where the binding sits, and why a catalog withholds
  an entry instead of qualifying it.
- [asking-the-installation.md](asking-the-installation.md) — the order an
  installation's own answers are looked up in, how the probe is delivered, and
  what a fallback owes the caller.

What belongs in the knowledge base at all, and what is verified against which
core checkout before it is written there, is in
[AGENTS.md](../../AGENTS.md).
