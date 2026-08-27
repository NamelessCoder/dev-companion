<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Manual;

use TYPO3\DevCompanion\Http\Fetch;

/**
 * The Sphinx inventory one manual publishes, read once and revalidated after.
 *
 * The format is four comment lines and then everything else compressed with
 * zlib, one object per line: the name, its `domain:role`, a priority, the URI it
 * resolves to and the name it is displayed under, where a `-` stands for a
 * display name equal to the object's own. Two readers take it apart differently
 * — the manual search reads the pages and the permalink lookup reads everything
 * that is not one — so what they share is the fetch and the parse.
 *
 * The third comment line is the branch that answered, which is the whole of what
 * says a version was widened: the host redirects a manual it has no branch for
 * to `main` and says nothing about it in the status.
 */
final class Inventory
{
    /** The table of contents of one manual, in the form its own builder writes. */
    private const FILE = 'objects.inv';

    /** One object, as the decompressed listing writes it. */
    private const OBJECT = '/^(.+?) +(\S+:\S+) +(-?\d+) +(\S+) +(.*)$/';

    /**
     * Each inventory as it was last read, under the URL it came from.
     *
     * Every artefact on this host carries an `ETag` and answers `If-None-Match`
     * with a 304 and a zero-byte body, so the second read of a session pays one
     * round trip and no payload for an inventory that is still current. That is
     * what makes it affordable at all: it is 308 kB for TYPO3 Explained against
     * 19.9 kB for the compressed root, so a session that fetched it again every
     * time would cost the host more than the navigation tree it replaced.
     *
     * It is not held in `Http\Recent`, which holds an answer for a chosen while
     * because its source cannot say whether it is still current. This one can, so
     * there is no while to choose: a 304 is the host saying that what is held is
     * still its answer — `D-ANS-065`.
     *
     * @var array<string, array{etag: string, inventory: array{title: string, branch: string, objects: list<array{name: string, role: string, uri: string, display: string}>}}>
     */
    private static array $held = [];

    public function __construct(private readonly Fetch $reader) {}

    /** What a reader handed in leaves behind: another reader is another host. */
    public static function forget(): void
    {
        self::$held = [];
    }

    /**
     * The inventory of the manual published at one base URL.
     *
     * A read that failed answers the same way a 304 does, with what is held. The
     * objects are what this host published, and a caller reaching one of them
     * while the host is down gets a stale answer rather than the book
     * disappearing.
     *
     * @return array{title: string, branch: string, objects: list<array{name: string, role: string, uri: string, display: string}>}|null
     */
    public function of(string $base): ?array
    {
        $url = $base . self::FILE;
        $held = self::$held[$url] ?? null;
        $response = $this->reader->read($url, $held === null ? [] : ['If-None-Match: ' . $held['etag']]);
        if ($response['body'] === null) {
            return $held['inventory'] ?? null;
        }

        $inventory = self::parse($response['body']);
        if ($inventory === null) {
            return $held['inventory'] ?? null;
        }
        if ($response['etag'] !== null) {
            self::$held[$url] = ['etag' => $response['etag'], 'inventory' => $inventory];
        }

        return $inventory;
    }

    /**
     * One inventory taken apart.
     *
     * Null is a body that is not an inventory, which is what bot protection
     * answers with a 200 in front of it (`D-ANS-034`), and an empty object list
     * is a manual that answered.
     *
     * @return array{title: string, branch: string, objects: list<array{name: string, role: string, uri: string, display: string}>}|null
     */
    private static function parse(string $body): ?array
    {
        $header = explode("\n", $body, 5);
        $listing = @zlib_decode($header[4] ?? '');
        if (!is_string($listing)) {
            return null;
        }

        $objects = [];
        foreach (explode("\n", $listing) as $line) {
            if (preg_match(self::OBJECT, $line, $object) !== 1) {
                continue;
            }
            [, $name, $role, , $uri, $display] = $object;
            $objects[] = [
                'name' => $name,
                'role' => $role,
                'uri' => ltrim($uri, '/'),
                'display' => $display === '-' ? $name : $display,
            ];
        }

        return [
            'title' => self::comment($header[1] ?? '', 'Project'),
            'branch' => self::comment($header[2] ?? '', 'Version'),
            'objects' => $objects,
        ];
    }

    /** What one of the header comments states, or an empty string where it states nothing. */
    private static function comment(string $line, string $field): string
    {
        return preg_match('/^# ' . $field . ': *(.*)$/', $line, $stated) === 1 ? trim($stated[1]) : '';
    }
}
