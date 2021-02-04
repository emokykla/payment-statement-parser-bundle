<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Factory\SwedbankCsv;

use EMO\PaymentStatementParserBundle\Model\SwedbankCsv\AbstractSwedbankCsvPaymentRowModel;
use EMO\PaymentStatementParserBundle\Model\SwedbankCsv\SwedbankCsvPaymentAccruedInterestRowModel;
use EMO\PaymentStatementParserBundle\Model\SwedbankCsv\SwedbankCsvPaymentClosingBalanceRowModel;
use EMO\PaymentStatementParserBundle\Model\SwedbankCsv\SwedbankCsvPaymentOpeningBalanceRowModel;
use EMO\PaymentStatementParserBundle\Model\SwedbankCsv\SwedbankCsvPaymentTransactionRowModel;
use EMO\PaymentStatementParserBundle\Model\SwedbankCsv\SwedbankCsvPaymentTurnoverRowModel;
use RuntimeException;

use function sprintf;

class SwedbankCsvPaymentRowModelFactory
{
    /**
     * @param string[] $row
     */
    public function get(string $lineId, array $row): AbstractSwedbankCsvPaymentRowModel
    {
        $recordType = $row[AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_RECORD_TYPE] ?? null;
        switch ($recordType) {
            case AbstractSwedbankCsvPaymentRowModel::RECORD_TYPE_OPENING_BALANCE:
                return new SwedbankCsvPaymentOpeningBalanceRowModel($lineId, $row);
            case AbstractSwedbankCsvPaymentRowModel::RECORD_TYPE_TRANSACTION:
                return new SwedbankCsvPaymentTransactionRowModel($lineId, $row);
            case AbstractSwedbankCsvPaymentRowModel::RECORD_TYPE_TURNOVER:
                return new SwedbankCsvPaymentTurnoverRowModel($lineId, $row);
            case AbstractSwedbankCsvPaymentRowModel::RECORD_TYPE_CLOSING_BALANCE:
                return new SwedbankCsvPaymentClosingBalanceRowModel($lineId, $row);
            case AbstractSwedbankCsvPaymentRowModel::RECORD_TYPE_ACCRUED_INTEREST:
                return new SwedbankCsvPaymentAccruedInterestRowModel($lineId, $row);
            default:
                if ($recordType) {
                    throw new RuntimeException(
                        sprintf(
                            'Could create "%s" class instance, record type "%s" is not implemented.',
                            AbstractSwedbankCsvPaymentRowModel::class,
                            $recordType
                        )
                    );
                }

                throw new RuntimeException(sprintf('Could create "%s" class instance, record type is empty.', AbstractSwedbankCsvPaymentRowModel::class));
        }
    }
}
