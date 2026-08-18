# Offer the debrief as a prompt where the feedback channel is

**Serves:** feedback/2026-08-18-071603-every-finding-in-this-session-s-ten-feedbacks.md, D-FBK-048
**Priority:** normal

Move the debrief text out of the code block in
`documentation/records/asking-for-a-debrief.rst` into one file both sides read,
and register it in `Server\Factory::create()` as a prompt beside
`commit_message` — taking no arguments, and added only where
`Feedback\Channel::isAvailable()` is true, which is what `Tool\Registry` already
gates the two channel tools on. The page then includes that file rather than
carrying a second copy of it. `StdioServerTest` asserts the prompt list and is
where the new one is held; `documentation/usage/installing.rst` names
`commit_message` under *What comes with it* and needs the second name there.
