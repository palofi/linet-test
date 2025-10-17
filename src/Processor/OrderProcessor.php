<?php

declare(strict_types=1);

namespace App\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\UpdateOrderDto;
use App\Entity\Contract;
use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\Status;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @implements ProcessorInterface<UpdateOrderDto, Order|null>
 */
final readonly class OrderProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private OrderRepository $orderRepository,
    ) {
    }

    /**
     * @param UpdateOrderDto $data
     * @throws \DateMalformedStringException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Order
    {
        /** @var Order $order */
        $order = $this->orderRepository->find($uriVariables['id']);

        $customer = $this->entityManager->getRepository(Customer::class)->find($data->customerID);
        $contract = $this->entityManager->getRepository(Contract::class)->find($data->contractID);
        $status = $this->entityManager->getRepository(Status::class)->find($data->status);

        $deliveryDate = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $data->deliveryAt ?? '');
        if ($deliveryDate === false) {
            throw new \InvalidArgumentException('Invalid delivery date format');
        }
        $deliveryDateUtc = $deliveryDate->setTimezone(new \DateTimeZone('UTC'));

        $order->setOrderNumber($data->orderNumber);
        $order->setRequestedDeliveryAt($deliveryDateUtc);
        $order->setCustomer($customer);
        $order->setContract($contract);
        $order->setStatus($status);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }
}
