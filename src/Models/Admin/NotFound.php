<?php

namespace Bonnier\Willow\Base\Models\Admin;

class NotFound
{
    /** @var int */
    private $notFoundID;

    /** @var string|null */
    private $url;

    /** @var string|null */
    private $urlHash;

    /** @var string|null */
    private $locale;

    /** @var int */
    private $hits;

    /** @var bool */
    private $notificationSent;

    /** @var \DateTime */
    private $updatedAt;

    /** @var bool */
    private $ignored;

    public function __construct()
    {
        $this->notFoundID = 0;
        $this->url = null;
        $this->urlHash = null;
        $this->locale = null;
        $this->hits = 0;
        $this->notificationSent = false;
        $this->updatedAt = new \DateTime();
        $this->ignored = false;
    }

    public static function createFromArray(array $data): NotFound
    {
        $notFound = new self();
        $notFound->setID(intval(array_get($data, 'id', 0)))
            ->setUrl(array_get($data, 'url', null))
            ->setUrlHash(array_get($data, 'url_hash', null))
            ->setLocale(array_get($data, 'locale', null))
            ->setHits(intval(array_get($data, 'hits', 0)))
            ->setNotificationSent(boolval(array_get($data, 'notification_sent', false)))
            ->setUpdatedAt(new \DateTime(array_get($data, 'updated_at', 'now')))
            ->setIgnored(boolval(array_get($data, 'ignore_entry', false)));

        return $notFound;
    }

    /**
     * @return int
     */
    public function getID(): int
    {
        return $this->notFoundID;
    }

    /**
     * @param int $notFoundID
     * @return NotFound
     */
    public function setID(int $notFoundID): NotFound
    {
        $this->notFoundID = $notFoundID;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     * @return NotFound
     */
    public function setUrl(?string $url): NotFound
    {
        $this->url = $url;
        if (!$this->urlHash) {
            $this->urlHash = hash('md5', $this->url);
        }
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrlHash(): ?string
    {
        return $this->urlHash;
    }

    /**
     * @param string|null $urlHash
     * @return NotFound
     */
    public function setUrlHash(?string $urlHash): NotFound
    {
        if ($urlHash) {
            $this->urlHash = $urlHash;
        }
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * @param string|null $locale
     * @return NotFound
     */
    public function setLocale(?string $locale): NotFound
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @return int
     */
    public function getHits(): int
    {
        return $this->hits;
    }

    /**
     * @param int $hits
     * @return NotFound
     */
    public function setHits(int $hits): NotFound
    {
        $this->hits = $hits;
        return $this;
    }

    public function isNotificationSent(): bool
    {
        return $this->notificationSent;
    }

    public function setNotificationSent(bool $sent): NotFound
    {
        $this->notificationSent = $sent;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     * @return NotFound
     */
    public function setUpdatedAt(\DateTime $updatedAt): NotFound
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function isIgnored(): bool
    {
        return $this->ignored;
    }

    public function setIgnored(bool $ignored): NotFound
    {
        $this->ignored = $ignored;
        return $this;
    }

    public function toArray()
    {
        return [
            'id' => $this->notFoundID,
            'url' => $this->url,
            'url_hash' => $this->urlHash,
            'locale' => $this->locale,
            'hits' => $this->hits,
            'notification_sent' => $this->notificationSent ? 1 : 0,
            'ignore_entry' => $this->ignored ? 1 : 0,
        ];
    }
}
