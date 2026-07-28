---
date: 2026-07-28T14:54:59+00:00
category: missing-knowledge
status: open
tool: typo3_architecture_hint
---

# The hints "events-extension-points" and "dependency-injection-services" stay entirely abstract: "...

## Observation

The hints "events-extension-points" and "dependency-injection-services" stay entirely abstract: "Prefer modern TYPO3 event APIs for new extension points", "Use Services.yaml when explicit service wiring is needed". They never name the actual mechanism, and the combination actively points the wrong way — an agent reads "modern event API" plus "Services.yaml for explicit wiring" and registers the listener with a Services.yaml event.listener tag. In the checkout at the pinned revision that pattern no longer exists anywhere: 94 classes under typo3/sysext/*/Classes/ carry the #[AsEventListener] attribute and there is not a single event.listener tag left in any typo3/sysext/*/Configuration/Services.yaml.

## Query

task="add a new PSR-14 event and register a listener via dependency injection", paths=["typo3/sysext/core/Configuration/Services.yaml","typo3/sysext/backend/Classes/EventListener/DummyListener.php"]

## Suggestion

Name the concrete conventions in the events hint: listeners are registered with the #[AsEventListener] attribute on the class or method (optional identifier/event/before/after arguments), not with Services.yaml tags; event classes live in Classes/Event/ of the owning extension, are final and readonly where the payload is immutable, and are public API that needs a Feature-*.rst changelog entry. In the DI hint, add that autoconfiguration and autowiring are on by default for the core sysexts, so Services.yaml is only touched for explicit wiring such as non-autowirable scalars, aliases, or public services — with #[Autowire] as the in-class alternative.
