<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Model\PostLtCsv;

use DateTimeImmutable;

use function str_replace;

/**
 * Provides some helper methods to get formatted value from raw values in csv model.
 */
class PostLtCsvPaymentRowFormattedModel
{
    /** @var PostLtCsvPaymentRowModel */
    private $postLtCsvPaymentRowModel;

    public function __construct(PostLtCsvPaymentRowModel $postLtCsvPaymentRowModel)
    {
        $this->postLtCsvPaymentRowModel = $postLtCsvPaymentRowModel;
    }

    public function getAmountInCents(): int
    {
        return (int) ((float) $this->postLtCsvPaymentRowModel->getRawAmount() * 100);
    }

    public function getPaymentDateObject(): DateTimeImmutable
    {
        $dateString = $this->fixDateStringFormat($this->postLtCsvPaymentRowModel->getRawPaymentDate());

        return new DateTimeImmutable($dateString);
    }

    public function getBankTransferDateObject(): DateTimeImmutable
    {
        $dateString = $this->fixDateStringFormat($this->postLtCsvPaymentRowModel->getRawBankTransferDate());

        return new DateTimeImmutable($dateString);
    }

    /**
     * Converts '2017.08.31' to '2017-08-31' which will be accepted by DateTime constructor.
     */
    private function fixDateStringFormat(string $dateString): string
    {
        return str_replace('.', '-', $dateString);
    }
}
