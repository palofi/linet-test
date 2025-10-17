<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class EntityExists extends Constraint
{
    public string $message = '{{ entity }} with ID "{{ value }}" does not exist.';

    public function __construct(
        /** @param class-string $entity */
        public string $entity,
        ?array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct([], $groups, $payload);
    }
}
