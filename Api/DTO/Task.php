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
    public const STATUS_ACTIVE = 'active';
    public const STATUS_STOPPED = 'stopped';
    public const STATUS_CANCELED = 'canceled';

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
     * In minutes.
     * 
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

    public function __construct(string $url, string $status, string $platform, int $period = 0, int $timeout = 0)
    {
        $this->url = $url;
        $this->status = $status;
        $this->period = $period;
        $this->timeout = $timeout;
        $this->platform = $platform;
    }

    public static function makeFromArray(array $taskArray): Task
    {
        $task = new self($taskArray['url'], $taskArray['status'], $taskArray['platform'], $taskArray['period'], $taskArray['timeout']);

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

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'status' => $this->getStatus(),
            'url' => $this->getUrl(),
            'period' => $this->getPeriod(),
            'createdAt' => $this->getCreatedAt() ? $this->getCreatedAt()->format(DateTimeInterface::ATOM) : null,
            'updatedAt' => $this->getUpdatedAt() ? $this->getUpdatedAt()->format(DateTimeInterface::ATOM) : null,
            'triggeredAt' => $this->getTriggeredAt() ? $this->getTriggeredAt()->format(DateTimeInterface::ATOM) : null,
            'platform' => $this->getPlatform(),
            'timeout' => $this->getTimeout(),
            'totalJobCount' => $this->getTotalJobCount(),
            'totalErrorCount' => $this->getTotalErrorCount(),
            'errorCount' => $this->getErrorCount(),
        ];
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

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getPeriod(): int
    {
        return $this->period;
    }

    public function setPeriod(int $period): void
    {
        $this->period = $period;
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
