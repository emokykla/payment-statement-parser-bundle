<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Tests\Unit\Model\PostLtCsv;

use DateTimeImmutable;
use EMO\PaymentStatementParserBundle\Model\PostLtCsv\PostLtCsvPaymentRowFormattedModel;
use EMO\PaymentStatementParserBundle\Model\PostLtCsv\PostLtCsvPaymentRowModel;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \EMO\PaymentStatementParserBundle\Model\PostLtCsv\PostLtCsvPaymentRowFormattedModel
 */
class PostLtCsvPaymentRowFormattedModelTest extends TestCase
{
    /**
     * @covers ::getAmountInCents
     *
     * @dataProvider getAmountInCentsProvider
     */
    public function testGetAmountInCents(string $amount, int $expectedAmountInCents): void
    {
        /* setup */
        $row = [PostLtCsvPaymentRowModel::CSV_COLUMN_KEY_AMOUNT => $amount];
        /* do */
        $postLtCsvPaymentRowModel = new PostLtCsvPaymentRowModel($lineId = 'line-1', $row, $sourceString = 'source string');
        $postLtCsvPaymentRowFormattedModel = new PostLtCsvPaymentRowFormattedModel($postLtCsvPaymentRowModel);
        /* assert */
        self::assertSame($expectedAmountInCents, $postLtCsvPaymentRowFormattedModel->getAmountInCents());
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

    /**
     * @covers ::getPaymentDateObject
     */
    public function testGetPaymentDateObject(): void
    {
        /* setup */
        $row = [PostLtCsvPaymentRowModel::CSV_COLUMN_KEY_PAYMENT_DATE => '2011.12.13'];
        /* do */
        $postLtCsvPaymentRowModel = new PostLtCsvPaymentRowModel($lineId = 'line-1', $row, $sourceString = 'source string');
        $postLtCsvPaymentRowFormattedModel = new PostLtCsvPaymentRowFormattedModel($postLtCsvPaymentRowModel);
        /* assert */
        self::assertEquals(new DateTimeImmutable('2011-12-13 00:00:00'), $postLtCsvPaymentRowFormattedModel->getPaymentDateObject());
    }

    /**
     * @covers ::getBankTransferDateObject
     */
    public function testGetBankTransferDateObject(): void
    {
        /* setup */
        $row = [PostLtCsvPaymentRowModel::CSV_COLUMN_KEY_BANK_TRANSFER_DATE => '2011.12.13'];
        /* do */
        $postLtCsvPaymentRowModel = new PostLtCsvPaymentRowModel($lineId = 'line-1', $row, $sourceString = 'source string');
        $postLtCsvPaymentRowFormattedModel = new PostLtCsvPaymentRowFormattedModel($postLtCsvPaymentRowModel);
        /* assert */
        self::assertEquals(new DateTimeImmutable('2011-12-13 00:00:00'), $postLtCsvPaymentRowFormattedModel->getBankTransferDateObject());
    }
}
