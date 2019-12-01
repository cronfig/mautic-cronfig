<?php
/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2019 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\Api\DTO;

use DateTimeInterface;
use DateTimeImmutable;

class Task
{
    /**
     * @var string|null
     */
    private $id;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $status;

    /**
     * @var int
     */
    private $period;

    /**
     * @var int
     */
    private $timeout;

    /**
     * @var string
     */
    private $platform;

    /**
     * @var DateTimeInterface|null
     */
    private $createdAt;

    /**
     * @var DateTimeInterface|null
     */
    private $updatedAt;

    /**
     * @var DateTimeInterface|null
     */
    private $triggeredAt;

    /**
     * @var int|null
     */
    private $totalJobCount;

    /**
     * @var int|null
     */
    private $totalErrorCount;

    /**
     * @var int|null
     */
    private $errorCount;

    public function __construct(string $url, string $status, int $period, int $timeout, string $platform)
    {
        $this->url      = $url;
        $this->status   = $status;
        $this->period   = $period;
        $this->timeout  = $timeout;
        $this->platform = $platform;
    }

    public static function makeFromArray(array $taskArray): Task
    {
        $task = new self($taskArray['url'], $taskArray['status'], $taskArray['period'], $taskArray['timeout'], $taskArray['platform']);

        $task->setId($taskArray['id']);
        $task->setCreatedAt(new DateTimeImmutable($taskArray['createdAt']));
        $task->setUpdatedAt(new DateTimeImmutable($taskArray['updatedAt']));
        $task->setTotalJobCount($taskArray['totalJobCount']);
        $task->setTotalErrorCount($taskArray['totalErrorCount']);
        $task->setErrorCount($taskArray['errorCount']);

        if (isset($taskArray['triggeredAt'])) {
            $task->setTriggeredAt(new DateTimeImmutable($taskArray['triggeredAt']));
        }

        return $task;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getPeriod(): int
    {
        return $this->period;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function getPlatform(): string
    {
        return $this->platform;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getTriggeredAt(): ?DateTimeInterface
    {
        return $this->triggeredAt;
    }

    public function setTriggeredAt(DateTimeInterface $triggeredAt): void
    {
        $this->triggeredAt = $triggeredAt;
    }

    public function getTotalJobCount(): ?int
    {
        return $this->totalJobCount;
    }

    public function setTotalJobCount(int $totalJobCount): void
    {
        $this->totalJobCount = $totalJobCount;
    }

    public function getTotalErrorCount(): ?int
    {
        return $this->totalErrorCount;
    }

    public function setTotalErrorCount(int $totalErrorCount): void
    {
        $this->totalErrorCount = $totalErrorCount;
    }

    public function getErrorCount(): ?int
    {
        return $this->errorCount;
    }

    public function setErrorCount(int $errorCount): void
    {
        $this->errorCount = $errorCount;
    }
}
