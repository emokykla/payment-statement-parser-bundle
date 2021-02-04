<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Tests\Service;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

use function get_class;
use function is_object;
use function sprintf;

class ViolationUtils
{
    /**
     * Returns violations as string that contain property path, message and invalid value. It makes easier to assert violations in tests.
     *
     * @return string[]
     */
    public static function stringify(ConstraintViolationListInterface $constraintViolationList): array
    {
        $violations = [];
        if ($constraintViolationList->count() > 0) {
            /** @var ConstraintViolationInterface $constraintViolation */
            foreach ($constraintViolationList as $constraintViolation) {
                $invalidValue = $constraintViolation->getInvalidValue();
                $violations[] = sprintf(
                    '%s %s Value: "%s".',
                    $constraintViolation->getPropertyPath(),
                    $constraintViolation->getMessage(),
                    is_object($invalidValue) ? sprintf('object of "%s" class', get_class($invalidValue)) : $invalidValue
                );
            }
        }

        return $violations;
    }
}
