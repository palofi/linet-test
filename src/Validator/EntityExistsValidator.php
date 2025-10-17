<?php

declare(strict_types=1);

namespace App\Validator;

use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class EntityExistsValidator extends ConstraintValidator
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     * @throws ReflectionException
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (! $constraint instanceof EntityExists) {
            throw new UnexpectedTypeException($constraint, EntityExists::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        /** @var class-string $entityClass */
        $entityClass = $constraint->entity;
        $repository = $this->entityManager->getRepository($entityClass);

        $entity = $repository->find($value);

        if (null === $entity) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', (string) $value)
                ->setParameter('{{ entity }}', $this->getEntityName($entityClass))
                ->addViolation();
        }
    }

    /**
     * @param class-string $entityClass
     * @throws ReflectionException
     */
    private function getEntityName(string $entityClass): string
    {
        return (new ReflectionClass($entityClass))->getShortName();
    }
}
