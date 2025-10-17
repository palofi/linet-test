<?php

declare(strict_types=1);

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::REQUEST, priority: 1000)]
final readonly class RequestLoggerListener
{
    public function __construct(
        #[Autowire(service: 'monolog.logger.request_log')]
        private LoggerInterface $requestLogLogger
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        if (! $event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        $logData = [
            'id' => uniqid('req_', true),
            'timestamp' => date('Y-m-d H:i:s'),
            'method' => $request->getMethod(),
            'uri' => $request->getRequestUri(),
            'headers' => $request->headers->all(),
            'query_params' => $request->query->all(),
            'request_data' => $this->getRequestData($request),
            'client_ip' => $request->getClientIp(),
            'user_agent' => $request->headers->get('User-Agent'),
        ];

        $this->requestLogLogger->info('HTTP Request received', $logData);
    }

    /**
     * @return array<string, mixed>
     */
    private function getRequestData(Request $request): array
    {
        $content = $request->getContent();

        if (empty($content)) {
            return [];
        }

        $contentType = $request->headers->get('Content-Type', '');

        if (str_contains($contentType, 'application/json')) {
            return $request->toArray();
        }

        if (str_contains($contentType, 'application/x-www-form-urlencoded')) {
            return $request->request->all();
        }

        return [
            'raw_content' => $content,
        ];
    }
}
