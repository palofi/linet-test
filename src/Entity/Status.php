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

    public function setId(StatusEnum $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(?string $userName): static
    {
        $this->userName = $userName;
        return $this;
    }

    public function getUserFullName(): ?string
    {
        return $this->userFullName;
    }

    public function setUserFullName(?string $userFullName): static
    {
        $this->userFullName = $userFullName;
        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static
    {
        if (! $this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setStatus($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            if ($order->getStatus() === $this) {
                $order->setStatus(null);
            }
        }

        return $this;
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
