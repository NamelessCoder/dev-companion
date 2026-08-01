# Run the next open feedback's query against the server as it is now

**Serves:** feedback/
**Every:** session
**Run:** bin/cli feedback:next

Take the one the listing hands over and no more: run the query that produced it
against the server as it is now, then give it one of three answers — close it,
trim it to the half that is still open, or add the todo below that takes it on,
and if that changes the order, move the queue before starting. A feedback is
evidence about a version of this server that may no longer exist, which is why
it is re-run rather than read. Write the judgement where judgements live: into
the decision it was made against, or into a new one where nothing says it yet,
because the commit that closes a feedback is the one place nobody can search
afterwards. Say which of the three it got and what the re-run showed, so
somebody who disagrees can say so. This comes round only when the queue is
empty, because a feedback that became a todo has already been judged and doing
it is what is owed next.
