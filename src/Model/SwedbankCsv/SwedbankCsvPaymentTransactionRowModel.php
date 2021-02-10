<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Model\SwedbankCsv;

use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Mapping\ClassMetadata;

use function implode;
use function sprintf;

class SwedbankCsvPaymentTransactionRowModel extends AbstractSwedbankCsvPaymentRowModel
{
    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        /**
         * @see AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_RECORD_TYPE
         */
        $availableRecordTypes = [
            self::RECORD_TYPE_TRANSACTION,
        ];
        $metadata->addPropertyConstraints(
            self::PROPERTY_RECORD_TYPE,
            [
                new Choice(
                    [
                        'message' => sprintf(
                            '[%d column] The value you selected is not a valid choice. Valid choices: "%s".',
                            self::INPUT_KEY_RECORD_TYPE,
                            implode('", "', $availableRecordTypes)
                        ),
                        'choices' => $availableRecordTypes,
                    ]
                ),
            ]
        );
        /**
         * @see AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_TRANSACTION_TYPE
         */
        $availableTransactionTypes = [
            self::TRANSACTION_TYPE_MK,
        ];
        $metadata->addPropertyConstraints(
            self::PROPERTY_TRANSACTION_TYPE,
            [
                new Choice(
                    [
                        'message' => sprintf(
                            '[%d column] The value you selected is not a valid choice. Valid choices: "%s".',
                            self::INPUT_KEY_TRANSACTION_TYPE,
                            implode('", "', $availableTransactionTypes)
                        ),
                        'choices' => $availableTransactionTypes,
                    ]
                ),
            ]
        );
    }
}
