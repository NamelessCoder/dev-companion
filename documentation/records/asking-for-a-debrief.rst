Asking for a debrief
====================

A session in a client this repository can read leaves a transcript, and which
skills activated and which tools were called are evidence in it. A session in
somebody else's agent leaves nothing here. The only thing that can report such a
run is the session itself, and it will not do so unprompted — an agent that
finished its task considers itself done.

So it is asked, in a message of its own **after** the work is finished. Before
or alongside it, the debrief becomes part of the task: an agent told it will be
asked which tools helped calls tools to have an answer. The prompt below is
generic on purpose — it names no scenario, no skill and no tool, so the same
text works after a review, an implementation, or a question the server could not
answer at all.

What it asks for is the report, never the shape of the feedback. What each field
wants — one feedback per subject, the task named in the first line, where to
read the model identifier rather than remember it — belongs to
``typo3_feedback_record`` and is written in its parameters, which is the only
documentation a client actually reads. A prompt that restates it is a second
copy that ages, and it would only reach the sessions somebody handed it to.

Half of what it asks about did not happen. The calls a session made are in front
of it; the skill that never activated, the tool it passed over and the question
it never asked leave nothing behind. A list that asks only what happened
confirms the surface the server already has —
`D-AUD-003 <../../decisions/audience/aud-003-the-instructions-carry-the-entry-point.md>`_
is a session the published skill's body would have carried, which never loaded
it and went through Bash instead.

The other thing the list has to work for is the knowledge. A session reports
what it did, and what it did is call tools, so bullets weighted towards skills,
calls and names come back describing skills, calls and names while the corpus
the task actually turned on goes unmentioned. Three of them ask about it
instead: the answer that stopped one step short, which is what the corpus fails
at rather than absence; the wish, asked without the scope test the session would
otherwise apply to it; and what was established elsewhere, asked for all of it
and sorted here rather than there —
`D-FBK-047 <../../decisions/feedback/fbk-047-the-debrief-asks-what-an-answer-left-out-and-what-the-session-wanted.md>`_.

The documents are asked about on their own because the model never picks one. A
resource is chosen by the host application or by the user rather than mid-task,
so a client that lists none leaves the session nothing to find. What that
surface is and what a picker chooses by:
:doc:`the resource surface <../server/resources/index>`. It is the half this
side is blindest to — the server sees the calls that were made, and a list
nobody rendered makes no call at all.

.. code-block:: text

    The work is done. What follows is a debrief about the TYPO3 knowledge server you
    had available, not about the repository you just worked in. Change no files.

    That server records feedback about itself with typo3_feedback_record, and the
    people who maintain it read them. Report the session you just had from your own
    transcript, not from how it felt. Where that transcript begins at a summary of
    earlier turns, say so and answer for the part you can see: what is missing from
    your window is not the same as what did not happen.

    - Which of its skills you activated, whether the skill fitted the task, and what
      in it you would keep or drop. Name the skill. If none activated, say so — that
      is a result.
    - Which part of the task no skill carried: the step you worked out for yourself
      and would work out again in the next session. Say what you were doing at the
      moment one would have had to activate, and in which words — the request, the
      symptom, the files you had open. A skill is chosen on its description alone,
      so one that exists and stayed shut is a different finding from one that is
      not there.
    - Which tool calls the task actually needed, in the order you made them, and how
      many round trips each answer cost. Walk the whole list rather than the calls
      you remember as notable; the ones that returned nothing are what memory drops.
      Name the ones you would not make again: a
      lookup that returned nothing usable, one you had to repeat with different
      arguments to get an answer, one that only restated what the previous answer
      already said. Where several of them went into one question, say which single
      call would have settled it and what it would have had to return.
    - Which of the documents it offers as resources you read whole, and whether your
      client showed you that list at all. They are picked out of a list rather than
      called mid-task, so a session can finish without learning they exist. Where you
      read one, say whether it carried the procedure end to end or sent you back to a
      search. Where you assembled the steps yourself, say what the page you wanted
      would have been called.
    - What you never put to it. A question you took elsewhere because you did not
      expect an answer, or a tool you read and passed over, is a finding the server
      has no other way of learning — it sees the calls that were made and nothing
      else. Name what you assumed, and whether it held.
    - Where a name did not mean what it said: a tool whose name or description
      promised another answer, one you would not have found from its name, a word
      you searched with that the server spells another way. That holds for what it
      answered as much as for what it is called — knowledge you took to be missing
      may have been there under a term you did not try. Say how you found the tools
      you did reach — from the list, from what the server told you at the start, or
      from a guess that landed. A name is what a client installed months ago still
      calls it by.
    - Where something went wrong: an error, an answer that was incorrect, an answer
      that did not hold on the TYPO3 version you were working against, an argument
      or a schema you had to guess at, a call you could not complete.
    - What the server saved you from — a wrong path you did not take, a file you did
      not have to read, an assumption it corrected before you acted on it. Be as
      concrete here as about the failures; what worked is what must not be broken
      later.
    - What you had to establish elsewhere — from the checkout, from your own
      knowledge, from the web. All of it, not only the part you take to be this
      server's subject.
    - Where an answer was right and stopped one step short: it named the API and not
      how to register it, the file and not what has to stand in it, the rule and not
      the case in front of you. Say which step you took yourself after reading it.
      That is a different finding from an answer that was missing, and you are the
      only one who can tell the two apart. Read the answers again rather than
      recalling them, and where one reported a gap about itself — a confidence it
      qualified, a route it said it had none for — say whether you acted on it.
    - What you would have wanted from it: the one answer, tool or page that would
      have made this session shorter. Do not weigh it against what you take this
      server to be for — where its boundary runs is a question for the people
      maintaining it, and a wish dropped as out of scope is the one they never hear.
    - What this list did not ask you about, and you would report anyway.

    File what you find with that tool. What each of its parameters wants, it says
    itself — read them there and fill in every one you can, including the model you
    are running as.

    Then tell me which feedback you filed and what each one says, and what this
    session cost: the tokens, the number of calls, the time it ran, whatever your
    client reports. Read those where they are written down rather than estimating
    them, name what your client does not report at all, and say whether the figure
    covers this debrief as well.


Paste it verbatim and add nothing — no tool names, no hint about what the last
session reported. The summary the agent gives afterwards is not what was
recorded; the feedback is, and ``typo3_feedback_list`` is where it is read back.

The cost at the end is for whoever pasted the prompt, and it reaches no file.
`D-FBK-020 <../../decisions/feedback/fbk-020-a-session-is-charged-per-call-so-the-calls-are-what-is-budgeted.md>`_
measured what a session costs from the transcripts of this repository's own
worktree sessions, and the sessions this prompt is handed to are the ones
nothing here can read. What comes back is one session's own figure, worth what
the client behind it reports — which is why it is read rather than estimated,
and why a client that reports none of it is named. It is asked last for the
reason the tools are: an agent that knows its calls will be counted makes fewer
of them, and what gets reported is then not the run that would have happened.

The one qualification it carries is
`R-FBK-012 <../../requirements/feedback/fbk-012-a-debrief-reports-the-window-the-session-could-see.md>`_.
The prompt is where the transcript is asked for, so it is where a session whose
transcript begins at a summary is asked to say so.
