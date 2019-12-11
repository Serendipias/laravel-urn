<?php

namespace Serendipias\Urn\Contracts;

/**
 * Interface UrnIdentifiable
 * @package Serendipias\Urn\Contracts
 * @url https://tools.ietf.org/html/rfc7841#section-2
 * @url https://en.wikipedia.org/wiki/Uniform_Resource_Name
 */
interface UrnIdentifiable
{
    /**
     * The namespace identifier, and may include letters, digits, and -.
     *
     * @return string
     */
    public function getNid(): string;

    /**
     * The NID is followed by the namespace-specific string <NSS>, the
     * interpretation of which depends on the specified namespace.
     * The NSS may contain ASCII letters and digits, and many punctuation
     * and special characters. Disallowed ASCII and Unicode characters may
     * be included if percent-encoded.
     *
     * @return string
     */
    public function getNss(): string;

    /**
     * Get the identifier used to build the urn.
     *
     * @return string
     */
    public function getIdentifierKey(): string;
}
