<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Model\SwedbankCsv;

use DateTimeImmutable;

/**
 * Provides some helper methods to get formatted value from raw values in csv model.
 */
class SwedbankCsvPaymentTransactionFormattedRowModel
{
    /** @var SwedbankCsvPaymentTransactionRowModel */
    private $swedbankCsvPaymentTransactionRowModel;

    public function __construct(SwedbankCsvPaymentTransactionRowModel $swedbankCsvPaymentTransactionRowModel)
    {
        $this->swedbankCsvPaymentTransactionRowModel = $swedbankCsvPaymentTransactionRowModel;
    }

    public function getAmountInCents(): int
    {
        return (int) ((float) $this->swedbankCsvPaymentTransactionRowModel->getAmount() * 100);
    }

    public function getDetailsModel(): SwedbankCsvPaymentTransactionDetailsModel
    {
        return new SwedbankCsvPaymentTransactionDetailsModel($this->swedbankCsvPaymentTransactionRowModel->getDetails());
    }

    public function getTransactionDate(): DateTimeImmutable
    {
        return new DateTimeImmutable($this->swedbankCsvPaymentTransactionRowModel->getTransactionDate());
    }
}
