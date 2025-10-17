<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\StatusEnum;
use App\Repository\StatusRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: StatusRepository::class)]
final class Status
{
    public const string GROUP_READ = 'status:read';

    #[ORM\Id]
    #[ORM\Column(type: 'string', enumType: StatusEnum::class)]
    #[Groups([self::GROUP_READ])]
    private ?StatusEnum $id = null;

    #[ORM\Column(length: 255)]
    #[Groups([self::GROUP_READ])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups([self::GROUP_READ])]
    #[Context([
        DateTimeNormalizer::FORMAT_KEY => DateTimeInterface::ATOM,
        DateTimeNormalizer::TIMEZONE_KEY => 'Europe/Prague',
    ])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $userName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $userFullName = null;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'status')]
    private Collection $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?StatusEnum
    {
        return $this->id;
    }

    public function setId(StatusEnum $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(?string $userName): void
    {
        $this->userName = $userName;
    }

    public function getUserFullName(): ?string
    {
        return $this->userFullName;
    }

    public function setUserFullName(?string $userFullName): void
    {
        $this->userFullName = $userFullName;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): void
    {
        if (! $this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setStatus($this);
        }
    }

    public function removeOrder(Order $order): void
    {
        if ($this->orders->removeElement($order)) {
            if ($order->getStatus() === $this) {
                $order->setStatus(null);
            }
        }
    }

    /**
     * @return array<string, string|null>
     */
    #[Groups([self::GROUP_READ])]
    #[SerializedName('user')]
    public function getUser(): array
    {
        return [
            'userName' => $this->getUserName(),
            'fullName' => $this->getUserFullName(),
        ];
    }
}
