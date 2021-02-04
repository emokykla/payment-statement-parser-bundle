<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Model\SwedbankCsv;

use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;

use function sprintf;

class SwedbankCsvPaymentOpeningBalanceRowModel extends AbstractSwedbankCsvPaymentRowModel
{
    /** @noinspection PhpUnusedParameterInspection */
    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        /**
         * @see AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_ACCOUNT_NUMBER
         */
        $metadata->addConstraint(
            new Callback(
                [
                    'callback' => static function (
                        SwedbankCsvPaymentOpeningBalanceRowModel $swedbankCsvPaymentOpeningBalanceRowModel,
                        ExecutionContextInterface $context
                    ): void {
                        $context
                            ->buildViolation(sprintf('Validation for "%s" is not implemented. Add it when the need arises.', __CLASS__))
                            ->addViolation()
                        ;
                    },
                ]
            )
        );
    }
}
