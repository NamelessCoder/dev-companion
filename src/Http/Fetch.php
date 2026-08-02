<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Http;

use Typo3CmsMcp\Server\Factory;

/**
 * The one way this server reads a host outside itself.
 *
 * Two sources reach outside — the manuals at `docs.typo3.org` and the services
 * the core's own process runs through — and each carried its own `curl` block
 * with the same options in it. What that costs is not the duplicated lines: it
 * is that the timeouts, the redirect limit and the user agent are a policy, and
 * a policy in two places is two policies as soon as one of them is edited.
 *
 * The timeouts are the policy worth naming. A lookup runs inside a tool call
 * somebody is waiting on, so a host that is slow is a host that did not answer:
 * three seconds to connect, eight in total, and after that the caller is told
 * rather than kept.
 *
 * The user agent is deliberately this server's own and not a browser's. It says
 * who is calling, which is what a host operator needs to see; and where bot
 * protection sits in front of one, it is the browser-shaped agents that get the
 * challenge page — a plain client agent is what gets through.
 */
final class Fetch
{
    /** Seconds to reach the host at all. */
    public const CONNECT_TIMEOUT = 3;

    /** Seconds for the whole request, connection included. */
    public const TIMEOUT = 8;

    /**
     * How many redirects are followed. Documentation hosts move pages and
     * answer with one; a chain longer than this is a portal rather than a
     * source.
     */
    private const MAX_REDIRECTS = 3;

    /**
     * The transport, where a caller has one. Every test in this repository
     * passes one rather than reaching the network, which is what keeps the
     * suite the same offline and on a plane.
     *
     * @var (\Closure(string): ?string)|null
     */
    private readonly ?\Closure $transport;

    /** @param (\Closure(string): ?string)|null $transport */
    public function __construct(?\Closure $transport = null)
    {
        $this->transport = $transport;
    }

    /**
     * The body of a successful read, or null for everything else.
     *
     * Null is the whole error vocabulary here on purpose: a 404, a timeout and
     * a DNS failure are one answer to the caller of a lookup — the source did
     * not answer this question — and what to say about it belongs to the source
     * that knows what was being asked.
     *
     * @param array<int, string> $headers extra request headers, in `Name: value` form
     */
    public function get(string $url, array $headers = []): ?string
    {
        if ($this->transport !== null) {
            return ($this->transport)($url);
        }

        $handle = curl_init($url);
        if ($handle === false) {
            return null;
        }

        curl_setopt_array($handle, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => self::MAX_REDIRECTS,
            CURLOPT_CONNECTTIMEOUT => self::CONNECT_TIMEOUT,
            CURLOPT_TIMEOUT => self::TIMEOUT,
            CURLOPT_USERAGENT => 'typo3-cms-mcp/' . Factory::SERVER_VERSION,
            CURLOPT_HTTPHEADER => $headers,
        ]);
        $body = curl_exec($handle);
        $status = curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
        curl_close($handle);

        return is_string($body) && $status >= 200 && $status < 300 ? $body : null;
    }
}
