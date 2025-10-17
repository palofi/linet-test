<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Contract;
use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\Status;
use App\Enum\StatusEnum;
use App\Repository\ContractRepository;
use App\Repository\CustomerRepository;
use App\Repository\OrderRepository;
use App\Repository\StatusRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:import-orders',
    description: 'Import orders from orders-data.json into the database',
)]
final class ImportOrdersCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly OrderRepository $orderRepository,
        private readonly CustomerRepository $customerRepository,
        private readonly ContractRepository $contractRepository,
        private readonly StatusRepository $statusRepository,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $jsonPath = dirname($this->projectDir) . '/orders-data.json';

        if (! file_exists($jsonPath)) {
            $io->error("File orders-data.json not found at: $jsonPath");
            return Command::FAILURE;
        }

        $jsonContent = file_get_contents($jsonPath);
        if ($jsonContent === false) {
            $io->error('Failed to read orders-data.json');
            return Command::FAILURE;
        }

        $ordersData = json_decode($jsonContent, true, 512, JSON_THROW_ON_ERROR);
        if (! is_array($ordersData)) {
            $io->error('Invalid JSON format in orders-data.json');
            return Command::FAILURE;
        }

        $io->info('Starting import of ' . count($ordersData) . ' orders...');
        $importedCount = 0;

        foreach ($ordersData as $orderData) {
            try {
                $statusData = $orderData['status'];
                $statusEnum = StatusEnum::fromString($statusData['id']);
                $status = $this->statusRepository->find($statusData['id']);

                if (! $status) {
                    $status = new Status();
                    $status->setId($statusEnum);
                }

                $status->setName($statusData['name']);
                $status->setCreatedAt(new DateTimeImmutable($statusData['createdAt']));
                $status->setUserName($statusData['user']['userName']);
                $status->setUserFullName($statusData['user']['fullName']);

                $this->entityManager->persist($status);

                $customerData = $orderData['customer'];
                $customer = $this->customerRepository->find($customerData['id']);

                if (! $customer) {
                    $customer = new Customer();
                    $customer->setId($customerData['id']);
                }

                $customer->setName($customerData['name']);
                $this->entityManager->persist($customer);

                $contractData = $orderData['contract'];
                $contract = $this->contractRepository->find($contractData['id']);

                if (! $contract) {
                    $contract = new Contract();
                    $contract->setId($contractData['id']);
                }

                $contract->setName($contractData['name']);
                $this->entityManager->persist($contract);

                $order = $this->orderRepository->find($orderData['id']);

                if (! $order) {
                    $order = new Order();
                    $order->setId($orderData['id']);
                }

                $order->setOrderNumber($orderData['orderNumber']);
                $order->setCustomerOrderNumber($orderData['customerOrderNumber'] ?? '');
                $order->setCreatedAt(new DateTimeImmutable($orderData['createdAt']));

                if ($orderData['closedAt']) {
                    $order->setClosedAt(new DateTimeImmutable($orderData['closedAt']));
                }

                if ($orderData['requestedDeliveryAt']) {
                    $order->setRequestedDeliveryAt(new DateTimeImmutable($orderData['requestedDeliveryAt']));
                }

                $order->setStatus($status);
                $order->setCustomer($customer);
                $order->setContract($contract);

                $this->entityManager->persist($order);

                $importedCount++;
                $io->writeln("Imported order ID: {$orderData['id']} - {$orderData['orderNumber']}");

            } catch (\Exception $e) {
                $io->error("Failed to import order ID: {$orderData['id']} - " . $e->getMessage());
                continue;
            }
        }

        $this->entityManager->flush();

        $io->success("Successfully imported $importedCount orders!");

        return Command::SUCCESS;
    }
}
