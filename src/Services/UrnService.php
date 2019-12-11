<?php

namespace Serendipias\Urn\Services;

use League\Uri\Components\Fragment;
use League\Uri\Components\Query;
use Serendipias\Urn\Contracts\UrnIdentifiable;
use Serendipias\Urn\Exceptions\InvalidUrnException;
use Serendipias\Urn\Support\Urn;

/**
 * Class UrnService
 * @package Serendipias\Urn\Services
 *
 * @url https://tools.ietf.org/html/rfc8141
 */
class UrnService
{
    private const URN = 'urn';

    protected $service;
    protected $stage;
    protected $namespace;

    public function __construct(string $service, string $stage, string $namespace = null)
    {
        $this->service = $service;
        $this->stage = $stage;
        $this->namespace = $namespace;
    }

    public function encode(
        UrnIdentifiable $urnIdentifiable,
        string $rComponent = null,
        string $qComponent = null,
        string $fComponent = null
    ): Urn {
        return new Urn(
            sprintf(
                '%s:%s:%s:%s:%s',
                static::URN,
                $this->service,
                $this->stage,
                $this->validateNidOrFail($urnIdentifiable->getNid()),
                $this->createNss(
                    $this->encodeNss($urnIdentifiable->getNss()),
                    $rComponent,
                    $qComponent,
                    $fComponent
                )
            ),
            $this->namespace
        );
    }

    public function decode(string $urn): Urn
    {
        $parts = explode(':', $urn);
        if (count($parts) < 3) {
            throw new InvalidUrnException(sprintf('Urn %s must contain a least 3 parts', $urn));
        }

        if (static::URN !== strtolower($parts[0])) {
            throw new InvalidUrnException('Urn must start with "urn"');
        }

        $nss = implode(':', array_slice($parts, 4));

        preg_match_all(
            '/^(?<nss>.+?(?=(\?\+|\?=|#|$)))(\?\+(?<r>.+?(?=(\?=|#|$))))?(\?=(?<q>.+?(?=(#|$))))?(#(?<f>.+?(?=($))))?$/',
            $nss,
            $matches
        );

        $matches = array_filter(
            array_map(
                'array_filter',
                array_filter($matches, function ($item) {
                    return $item !== '' || $item !== null;
                })
            ), function ($item) {
            return ! empty($item);
        });

        return new Urn(
            sprintf(
                '%s:%s:%s:%s:%s',
                static::URN,
                $this->service,
                $this->stage,
                strtolower($parts[3]),
                $this->createNss(
                    urldecode($matches['nss'][0]),
                    $matches['r'][0] ?? null,
                    $matches['q'][0] ?? null,
                    $matches['f'][0] ?? null
                )
            ),
            $this->namespace
        );
    }

    private function createNss(
        string $nss,
        string $rComponent = null,
        string $qComponent = null,
        string $fComponent = null
    ) {
        $rComponent = null === $rComponent ? '' : '?+' . $rComponent;
        $qComponent = null === $qComponent ? '' : '?=' . new Query($qComponent);
        $fComponent = null === $fComponent ? '' : '#' . new Fragment($fComponent);

        return  $nss . $rComponent . $qComponent . $fComponent;
    }

    private function validateNidOrFail(string $nid): string
    {
        if (! preg_match('/^[\da-zA-Z]{0,30}$/', $nid)) {
            throw new InvalidUrnException('Only alphanumeric characters allowed.');
        }

        return $nid;
    }

    protected function encodeNss(string $nss)
    {
        $notAllowedList = '/[^a-zA-Z0-9\-\._~@!\$&\'()\*\+,;=\\/:]/';
        preg_match_all($notAllowedList, $nss, $matches);
        $replaceMap = [];
        foreach ($matches[0] as $match) {
            if (! isset($replaceMap[$match])) {
                $replaceMap[$match] = sprintf('[-percentage-]%x', ord($match));
            }
        }

        return str_replace(
            '[-percentage-]',
            '%',
            str_replace(
                array_keys($replaceMap),
                array_values($replaceMap),
                str_replace(array_keys($replaceMap), array_values($replaceMap), $nss)
            )
        );
    }
}
