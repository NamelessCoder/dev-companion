---
id: D-ANS-021
date: 2026-08-02
status: open
---

# D-ANS-021 — A manual query is told what short buys, because the index is a table of contents

**The live-manual search ranks a table of contents, so a query that names its
subject spends its weight on everything else.**

The page the session was after is in the index and has been all along. What sank
it is that `Record API` is the cheapest thing in a query that carries it, and
that three calls came back `answered` with six results each, in the shape a good
answer has.

## Evidence

- `feedback/2026-08-01-002928` re-run on 2026-08-02 against the server as it is
  now, `DocumentationLookup::answer()` in this worktree at `targetVersion: 14`.
  All three queries reproduce: `Record API Fluid template access record.header`
  returns *Multi-language Fluid templates* and `RecordAccessGrantedEvent`,
  `Record API access relation field` returns `RecordAccessGrantedEvent` and
  Extbase *Relations*, `Record API record get has` returns
  `PasswordHasBeenResetEvent` first. The feedback's account of what came back is
  accurate to the page.
- Its suggestion rests on a premise that is false. *Record objects* —
  `ApiOverview/Database/DatabaseRecords/RecordObjects.html` — is one of the 1230
  pages the three manual indexes contribute at 14.3, and it scores above zero on
  all three queries. It ranks 28, 13 and 11, and `limit` was 6. Nothing needs
  indexing.
- It scores 159 on each of the three, because `record` and `api` are the only
  terms of any of them it can match. The winners score 254 to 380 on words that
  have nothing to do with the subject: `has` reached `PasswordHasBeenResetEvent`
  through the compound split, `get` reached `getText` and `getEnv`, and `acces`
  reached `RecordAccessGrantedEvent`.
- The name of the thing asked about is the cheapest half of every query. Over
  those 1230 pages, `api` is carried by 635 of them and weighs 0.66, because
  `ApiOverview` is a path segment of most of TYPO3 Explained; `record` is
  carried by 32 and weighs 3.65. Every further word outweighs both — `acces` 16
  pages at 4.34, `get` 5 at 5.51, `has` 3 at 6.02, `relati` 1 at 7.11. So each
  word the session added moved the answer further from the page it wanted.
- `record.header` contributed the term `record` and nothing else.
  `TermSearch::terms()` keeps a dot inside a word, and `stem()` cuts anything
  over six characters to six, so the most specific thing the session knew
  arrived as the least specific term in the query.
- Short queries reach it, which is what `D-ANS-003` already said and this run
  confirms with the ranks above. `Record API` alone returns *Record objects*
  third; `record objects` returns it first.
- What is searched is a table of contents and nothing else: `title` at weight 4,
  `path` at 2, `manual` at 2. `Documentation::lookup()` fetches page bodies only
  after the ranking, for the pages it has already chosen, so no query has ever
  been matched against the text of a page.
- Nothing in the answer distinguishes these three calls from good ones. `status`
  was `answered` each time, with six results carrying titles, canonical URLs and
  excerpts, and the session had no way to see that the one term naming its
  subject had contributed least of all.

## Decided

- **Step 4 of the ladder, wording.** The rule the session needed is here and was
  delivered: "several short English queries" stands in the tool description, in
  the `queries` description, and in six skills. It did not take, because nothing
  says what "short" buys — that the index is a table of contents, and that a
  longer query re-aims rather than refines.
- **The suggestion is rejected on its premise and taken on what it is after.**
  The page is indexed, so the feedback is trimmed to the two round trips, which
  is the part that is real.
- **Step 1a was not the answer and was checked rather than assumed.**
  `bin/cli hints:probe` reaches `frontend-records` and `fluid-templates` for
  these queries, and neither carries the Record API's accessors. That is a
  knowledge gap about TYPO3, and it is not what this feedback reports: the
  session got its answer from the manual page in the end, and what it lost was
  the two calls before it. A `knowledge/` entry copying a manual page is what
  the manual lookup exists to avoid.
- **Queued rather than closed on the spot.** Both candidates below touch the
  declared schema or the answer shape of `typo3_documentation_lookup`, which
  [judging.md](../../documentation/feedback/judging.md) puts on the reviewed
  side of the autonomous line.
- **Two candidates, and neither is chosen here.** Say it where the call is
  composed — the descriptions state that page titles and section paths are what
  is matched, and that words beyond the subject re-aim the search. Or say it
  where the answer arrives — the result names what the match was on, or the
  shortest sub-query that still returns these pages, which is the shape
  `D-ANS-016` settled on for the changelog miss.
- Recorded against the answer rather than against the search. `R-ANS-007` is the
  scoring behaving exactly as designed, and `D-ANS-003` keeps retrieval lexical;
  what is missing sits between a correct ranking and what the caller is told
  about it.

## Assumed

- That the second candidate is the one that survives deferral. `D-AUD-003`
  established that tool descriptions are not a channel at all where a client
  defers them, and this session called the tool three times, so its schema had
  been fetched — which is evidence for the first candidate reaching a caller,
  from one session and one client.
- That a caller handed the shortest working sub-query follows it. That is the
  same assumption `D-ANS-016` left open, and nothing here measures it either.
- That the ranks hold as the manuals grow. They were read off the 14.3 indexes
  on one day, and a page added to `DatabaseRecords/` moves them.

## Wrong if

- The rule lands in the descriptions and in the answer, and a session still
  arrives with a five-word question and reports the same miss. Then the wording
  is not what failed, and what is left to suspect is the index itself: a table
  of contents cannot answer a question phrased in the words of an API, and
  matching section headings within a page is the next lever.
- A feedback reports the opposite cost — a two-word query answered with six
  pages about the wrong subject, where a longer one would have separated them.
  Then shortness is being sold as a rule where it is a trade, and the answer is
  a sentence about which words to keep rather than about how many.
- The same three queries reach *Record objects* after somebody retitles the page
  upstream. Then this was a title that did not match its subject rather than a
  property of the search, and one page's wording carried the whole finding.

## Since then

Built on 2026-08-02 as the second candidate, and the first is not built beside
it. What decided it is this entry's own **Assumed**: a description a client
defers reaches nobody, while the answer is read by every caller that gets one.
Its counter-evidence — that this session had fetched the schema — says the first
candidate would have reached this one caller, not that it reaches the next.

Every search result names what it was matched on: the query words the index
carried, as the stems that were searched for, and the field each was found in.
The answer says once, above the results, that page titles and section paths are
all there is to match. The reproducer re-run against the live 14.3 manuals now
opens with *Multi-language Fluid templates* —
`Matched on: fluid (title), templa (title)` — so the two words naming the
subject are visibly absent from the page that outranked it, which is the whole
of what the session could not see. `RecordAccessGrantedEvent` reads
`record (title), api (path), acces (title)`: it carries the subject and is still
the wrong page, which is the case no wording could have prevented.

Which field a term was found in is `TermSearch::score()`'s third return value
rather than a second pass in `Documentation`. The strongest field is a tie the
scoring already breaks, and deciding it twice is how the answer and the ranking
come to disagree.

The stems are reported as they were matched — `templa`, `acces` — rather than
the words they came from. That is what the search did, and it is also the only
place a caller can see that `record.header` arrived as `record`. Nothing
measures whether a caller reads a stem as a typo.
