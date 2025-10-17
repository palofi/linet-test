<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Contract;
use App\Entity\Customer;
use App\Entity\Status;
use App\Validator\EntityExists;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateOrderDto
{
    #[Assert\NotBlank(message: 'Order number is required')]
    #[Assert\Length(
        max: 20,
        maxMessage: 'Order number cannot be longer than {{ limit }} characters'
    )]
    public ?string $orderNumber = null;

    #[Assert\NotBlank(message: 'Delivery date is required')]
    #[Assert\DateTime(
        format: \DateTimeInterface::ATOM,
        message: 'Delivery date must be in ATOM format (Y-m-d\TH:i:sP)'
    )]
    public ?string $deliveryAt = null;

    #[Assert\NotBlank(message: 'Customer ID is required')]
    #[Assert\Type(
        type: 'integer',
        message: 'Customer ID must be a number'
    )]
    #[Assert\Positive(message: 'Customer ID must be a positive number')]
    #[EntityExists(entity: Customer::class)]
    public ?int $customerID = null;

    #[Assert\NotBlank(message: 'Contract ID is required')]
    #[Assert\Type(
        type: 'integer',
        message: 'Contract ID must be a number'
    )]
    #[Assert\Positive(message: 'Contract ID must be a positive number')]
    #[EntityExists(entity: Contract::class)]
    public ?int $contractID = null;

    #[Assert\NotBlank(message: 'Status is required')]
    #[Assert\Choice(
        choices: ['NEW', 'ACT', 'END'],
        message: 'Status must be one of: NEW, ACT, END'
    )]
    #[EntityExists(entity: Status::class)]
    public ?string $status = null;
}
