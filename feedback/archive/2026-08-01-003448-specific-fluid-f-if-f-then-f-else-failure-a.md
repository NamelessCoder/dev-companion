---
date: 2026-08-01T00:34:48+00:00
category: missing-knowledge
status: closed
closed: 2026-08-02
model: opencode/deepseek-v4-flash-free
tool: typo3-content-element-development, typo3_documentation_lookup
directory: /home/benji/projects/site-new
---

# Debrief of the TYPO3 14 testimonials session: the specific Fluid f:if/f:then/f:else failure. A te...

## Observation

Debrief of the TYPO3 14 testimonials session: the specific Fluid f:if/f:then/f:else failure. A testimonial template conditionally wrapped the company name in <f:link.typolink> when a link field was set. The template used <f:if condition="...link..."> with an <f:else> branch but the then-branch content (the f:link.typolink viewhelper) was written inline, without an explicit <f:then>. Result: the else branch rendered but the linked company rendered empty, even though the data (link=https://www.formfabrik.example, company='CTO, Formfabrik GmbH') was present in the template context. The user diagnosed it with 'you added the else but not the then' and 'list just frap the link viewhelper in <f:then>'. The fix was wrapping the link viewhelper in an explicit <f:then> sibling of <f:else>:
<f:if condition="{testimonial.data.link}"><f:then><f:link.typolink parameter="{testimonial.data.link}">{testimonial.data.company}</f:link.typolink></f:then><f:else>{testimonial.data.company}</f:else></f:if>
which made the external-URL link render. The IfViewHelper contract — that an <f:else> forces an explicit <f:then> and that viewhelper output inside an implicit then-branch can be swallowed — was not known and the documentation was not consulted until the user demanded it.

## Query

f:if with f:else but no explicit f:then swallows the inline then-branch / f:link.typolink output

## Suggestion

Document the IfViewHelper branch rule with this exact symptom: once an <f:else> is present, the then-branch must be an explicit <f:then>; inline content between <f:if> tags does not reliably render as the then-branch in that case, and viewhelper output (e.g. f:link.typolink) inside it renders empty. Include the working structure above as the canonical example.
