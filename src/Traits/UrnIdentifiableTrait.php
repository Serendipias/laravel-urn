<?php

namespace Serendipias\Urn\Traits;

use Illuminate\Support\Str;

trait UrnIdentifiableTrait
{
    public function getNid(): string
    {
        return Str::kebab((new \ReflectionClass($this))->getShortName());
    }

    public function getNss(): string
    {
        return $this->getKey();
    }

    /**
     * By default it uses model's primary key.
     *
     * @return string
     */
    public function getIdentifierKey(): string
    {
        return $this->getKeyName();
    }
}
