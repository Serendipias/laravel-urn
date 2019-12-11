<?php

namespace Serendipias\Urn\Support;

use Serendipias\Urn\Contracts\UrnIdentifiable;
use Serendipias\Urn\Exceptions\InvalidUrnException;
use Serendipias\Urn\Exceptions\NotFoundException;

class Urn
{
    /** @var string */
    private $urn;
    /** @var string */
    private $namespace;
    /** @var array */
    private $parts = [];

    public function __construct(string $urn, string $namespace = null)
    {
        $this->urn = $urn;
        $this->namespace = $namespace;
        $this->parts = explode(':', $urn);
    }

    public function __get($name)
    {
        if ('urn' !== $name) {
            return null;
        }

        return $this->getUrnString();
    }

    public function getUrnString(): ?string
    {
        return $this->urn;
    }

    public function find()
    {
        $class = sprintf('\\%s\\%s', $this->namespace, ucfirst($this->getNid()));

        if (! class_exists($class)) {
            return null;
        }

        $class = new $class;

        if (! $class instanceof UrnIdentifiable) {
            throw new InvalidUrnException(sprintf('class %s is not instance of %s', $class, UrnIdentifiable::class));
        }

        return $class::query()->where($class->getIdentifierKey(), $this->getNss())->first();
    }

    public function findOrFail()
    {
        if (! $result = $this->find()) {
            throw new NotFoundException();
        }

        return $result;
    }

    public function getNid(): string
    {
        return $this->parts[3] ?? null;
    }

    public function getNss(): string
    {
        return $this->parts[4] ?? null;
    }
}
