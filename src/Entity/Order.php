<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use App\Dto\UpdateOrderDto;
use App\Processor\OrderProcessor;
use App\Repository\OrderRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/order/list',
        ),
        new Get(
            uriTemplate: '/order/{id}',
        ),
        new Put(
            uriTemplate: '/order/{id}',
            input: UpdateOrderDto::class,
            processor: OrderProcessor::class,
        ),
    ],
    normalizationContext: [
        'groups' => [Order::GROUP_READ, Status::GROUP_READ, Customer::GROUP_READ, Contract::GROUP_READ],
    ],
)]
#[ApiFilter(DateFilter::class, properties: ['createdAt', 'closedAt'])]
#[ApiFilter(SearchFilter::class, properties: [
    'status.id' => 'exact',
])]
#[ApiFilter(OrderFilter::class, properties: ['id', 'createdAt', 'closedAt', 'orderNumber'])]
#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'orders')]
final class Order
{
    public const string GROUP_READ = 'order:read';

    #[ORM\Id]
    #[ORM\Column]
    #[Groups([Order::GROUP_READ])]
    private ?int $id = null;

    #[ORM\Column(length: 20, unique: true)]
    #[Groups([Order::GROUP_READ])]
    private ?string $orderNumber = null;

    #[ORM\Column(length: 255)]
    #[Groups([Order::GROUP_READ])]
    private string $customerOrderNumber = '';

    #[ORM\Column]
    #[Groups([Order::GROUP_READ])]
    #[Context([
        DateTimeNormalizer::FORMAT_KEY => DateTimeInterface::ATOM,
        DateTimeNormalizer::TIMEZONE_KEY => 'Europe/Prague',
    ])]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups([Order::GROUP_READ])]
    #[Context([
        DateTimeNormalizer::FORMAT_KEY => DateTimeInterface::ATOM,
        DateTimeNormalizer::TIMEZONE_KEY => 'Europe/Prague',
    ])]
    private ?DateTimeImmutable $closedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups([Order::GROUP_READ])]
    #[Context([
        DateTimeNormalizer::FORMAT_KEY => DateTimeInterface::ATOM,
        DateTimeNormalizer::TIMEZONE_KEY => 'Europe/Prague',
    ])]
    private ?DateTimeImmutable $requestedDeliveryAt = null;

    #[ORM\ManyToOne(targetEntity: Status::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([Order::GROUP_READ])]
    private ?Status $status = null;

    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([Order::GROUP_READ])]
    private ?Customer $customer = null;

    #[ORM\OneToOne(targetEntity: Contract::class, inversedBy: 'order')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([Order::GROUP_READ])]
    private ?Contract $contract = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(?string $orderNumber): void
    {
        $this->orderNumber = $orderNumber;
    }

    public function getCustomerOrderNumber(): string
    {
        return $this->customerOrderNumber;
    }

    public function setCustomerOrderNumber(string $customerOrderNumber): void
    {
        $this->customerOrderNumber = $customerOrderNumber;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getClosedAt(): ?DateTimeImmutable
    {
        return $this->closedAt;
    }

    public function setClosedAt(?DateTimeImmutable $closedAt): void
    {
        $this->closedAt = $closedAt;
    }

    public function getRequestedDeliveryAt(): ?DateTimeImmutable
    {
        return $this->requestedDeliveryAt;
    }

    public function setRequestedDeliveryAt(?DateTimeImmutable $requestedDeliveryAt): void
    {
        $this->requestedDeliveryAt = $requestedDeliveryAt;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): void
    {
        $this->status = $status;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function getContract(): ?Contract
    {
        return $this->contract;
    }

    public function setContract(?Contract $contract): void
    {
        $this->contract = $contract;
    }
}
