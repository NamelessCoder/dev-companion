<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Http;

use TYPO3\DevCompanion\Server\Factory;

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
 *
 * Compression is asked for on every read. docs.typo3.org answers
 * `Vary: Accept-Encoding` and `Content-Encoding: gzip` on everything tried, and
 * the TYPO3 Explained root is 169 kB plain against 19.9 kB compressed — so the
 * option that was missing cost this server 8.5 times the payload and the host
 * 8.5 times the egress, on every manual lookup.
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
    public function get(string $url, array $headers = [], ?string $agent = null): ?string
    {
        return $this->read($url, $headers, $agent)['body'];
    }

    /**
     * What a host that challenges browsers answers instead.
     *
     * The protection in front of one of the sources here inverts the usual
     * repair: a browser-shaped agent gets a 200 and a challenge page, and a
     * plain client agent gets the answer. So where a body did not parse as what
     * was asked for, the retry is not "look more like a browser" but the
     * opposite, and it is worth one extra round trip because the alternative is
     * telling the caller that the source is down when it is not.
     */
    public const PLAIN_AGENT = 'curl/8.5.0';

    /**
     * What an API prefixes its JSON with so a browser cannot execute the
     * response as a script. It is not JSON and has to come off before the body
     * parses; a body that does not start with it is passed through untouched,
     * because anything else at the head is a portal rather than a guard.
     */
    private const XSSI_PREFIX = ")]}'";

    /**
     * The JSON a body carries, or null for everything that is not JSON.
     *
     * The sources that reach outside this package read JSON and nothing else
     * (`D-ANS-034`). A page with a 200 in front of it is what bot protection
     * and captive portals answer with, and the only safe reading of one is that
     * the question was not answered — so this returns null rather than letting
     * a caller decide how much of an HTML document looks like an answer.
     *
     * @return array<mixed>|null
     */
    public static function decode(?string $body): ?array
    {
        if ($body === null) {
            return null;
        }

        $body = ltrim($body);
        if (str_starts_with($body, self::XSSI_PREFIX)) {
            $body = ltrim(substr($body, strlen(self::XSSI_PREFIX)));
        }

        $decoded = json_decode($body, true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * The same read, with the status the host answered.
     *
     * One source needs it: an issue tracker answers 404 for an issue that does
     * not exist, and "there is no such issue" is a different answer to the
     * caller than "the tracker did not answer". Everything else collapses both
     * into a body it did not get, which is why `get()` is the shorter one.
     *
     * A transport is a body without a status, so an injected one reports 200
     * for what it returns and 0 for what it does not. What that leaves
     * untestable is the mapping of one status onto one answer, and the source
     * that does the mapping says so.
     *
     * The entity tag comes back beside them, because it is the whole of what a
     * caller needs to ask the same question again for nothing: docs.typo3.org
     * serves every artefact under `Cache-Control: public, no-cache` with an
     * `ETag`, and answers `If-None-Match` with a 304 and no body at all.
     *
     * @param array<int, string> $headers extra request headers, in `Name: value` form
     * @return array{status: int, body: ?string, etag: ?string}
     */
    public function read(string $url, array $headers = [], ?string $agent = null): array
    {
        if ($this->transport !== null) {
            $body = ($this->transport)($url);

            return ['status' => $body === null ? 0 : 200, 'body' => $body, 'etag' => null];
        }

        $handle = curl_init($url);
        if ($handle === false) {
            return ['status' => 0, 'body' => null, 'etag' => null];
        }

        $etag = null;
        curl_setopt_array($handle, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => self::MAX_REDIRECTS,
            CURLOPT_CONNECTTIMEOUT => self::CONNECT_TIMEOUT,
            CURLOPT_TIMEOUT => self::TIMEOUT,
            CURLOPT_USERAGENT => $agent ?? 'typo3-dev-companion/' . Factory::SERVER_VERSION,
            CURLOPT_HTTPHEADER => $headers,
            // The empty string is every encoding this build of curl can undo,
            // and curl undoes it before the body is returned.
            CURLOPT_ENCODING => '',
            CURLOPT_HEADERFUNCTION => static function ($handle, string $line) use (&$etag): int {
                if (stripos($line, 'etag:') === 0) {
                    $etag = trim(substr($line, strlen('etag:')));
                }

                return strlen($line);
            },
        ]);
        $body = curl_exec($handle);
        $status = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);

        return [
            'status' => $status,
            'body' => is_string($body) && $status >= 200 && $status < 300 ? $body : null,
            'etag' => $etag,
        ];
    }
}
