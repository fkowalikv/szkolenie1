<?php

namespace Loyalty\Events;

use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

abstract class Event
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var DateTimeImmutable
     */
    private $createdAt;

    /**
     * @var int
     */
    private $version;

    /**
     * Event constructor.
     */
    public function __construct()
    {
//        $this->id = Uuid::uuid4();
        $this->createdAt = new DateTimeImmutable();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @param int $version
     */
    public function setVersion(int $version): void
    {
        $this->version = $version;
    }
}