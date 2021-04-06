<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Tests\Unit\Model\SwedbankCsv;

use DateTimeImmutable;
use EMO\PaymentStatementParserBundle\Model\SwedbankCsv\AbstractSwedbankCsvPaymentRowModel;
use EMO\PaymentStatementParserBundle\Model\SwedbankCsv\SwedbankCsvPaymentTransactionFormattedRowModel;
use EMO\PaymentStatementParserBundle\Model\SwedbankCsv\SwedbankCsvPaymentTransactionRowModel;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \EMO\PaymentStatementParserBundle\Model\SwedbankCsv\SwedbankCsvPaymentTransactionFormattedRowModel
 */
class SwedbankCsvPaymentTransactionFormattedRowModelTest extends TestCase
{
    /**
     * @covers ::getTransactionDate
     */
    public function testGetTransactionDate(): void
    {
        /* setup */
        $row = [
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_RECORD_TYPE => $recordType = AbstractSwedbankCsvPaymentRowModel::RECORD_TYPE_TRANSACTION,
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_TRANSACTION_DATE => '2011-12-13',
        ];
        /* do */
        $swedbankCsvPaymentTransactionRowModel = new SwedbankCsvPaymentTransactionRowModel('line-1', $row, '');
        $swedbankCsvPaymentTransactionFormattedRowModel = new SwedbankCsvPaymentTransactionFormattedRowModel($swedbankCsvPaymentTransactionRowModel);
        /* assert */
        self::assertEquals(new DateTimeImmutable('2011-12-13 00:00:00'), $swedbankCsvPaymentTransactionFormattedRowModel->getTransactionDate());
    }

    /**
     * @covers ::getAmountInCents
     *
     * @dataProvider getAmountInCentsProvider
     */
    public function testGetAmountInCents(string $amount, int $expectedAmountInCents): void
    {
        /* setup */
        $row = [
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_RECORD_TYPE => $recordType = AbstractSwedbankCsvPaymentRowModel::RECORD_TYPE_TRANSACTION,
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_AMOUNT => $amount,
        ];
        /* do */
        $swedbankCsvPaymentTransactionRowModel = new SwedbankCsvPaymentTransactionRowModel('line-1', $row, '');
        $swedbankCsvPaymentTransactionFormattedRowModel = new SwedbankCsvPaymentTransactionFormattedRowModel($swedbankCsvPaymentTransactionRowModel);
        /* assert */
        self::assertSame($expectedAmountInCents, $swedbankCsvPaymentTransactionFormattedRowModel->getAmountInCents());
    }

    /** @return mixed[][] */
    public function getAmountInCentsProvider(): array
    {
        return [
            '0.' => [
                'amount' => '0.00',
                'expectedAmountInCents' => 0,
            ],
            '1.' => [
                'amount' => '0.01',
                'expectedAmountInCents' => 1,
            ],
            '2.' => [
                'amount' => '1.00',
                'expectedAmountInCents' => 100,
            ],
            '3.' => [
                'amount' => '12345.67',
                'expectedAmountInCents' => 1234567,
            ],
        ];
    }
}
