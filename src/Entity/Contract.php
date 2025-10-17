<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ContractRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ContractRepository::class)]
final class Contract
{
    public const string GROUP_READ = 'contract:read';

    #[ORM\Id]
    #[ORM\Column]
    #[Groups([self::GROUP_READ])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups([self::GROUP_READ])]
    private ?string $name = null;

    #[ORM\OneToOne(targetEntity: Order::class, mappedBy: 'contract')]
    private ?Order $order = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
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

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): static
    {
        if ($order === null && $this->order !== null) {
            $this->order->setContract(null);
        }

        if ($order !== null && $order->getContract() !== $this) {
            $order->setContract($this);
        }

        $this->order = $order;

        return $this;
    }
}
