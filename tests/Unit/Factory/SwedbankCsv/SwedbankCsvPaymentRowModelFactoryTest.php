<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Tests\Unit\Factory\SwedbankCsv;

use EMO\PaymentStatementParserBundle\Exception\InvalidStatementContentException;
use EMO\PaymentStatementParserBundle\Factory\SwedbankCsv\SwedbankCsvPaymentRowModelFactory;
use EMO\PaymentStatementParserBundle\Model\SwedbankCsv\AbstractSwedbankCsvPaymentRowModel;
use EMO\PaymentStatementParserBundle\Model\SwedbankCsv\SwedbankCsvPaymentAccruedInterestRowModel;
use EMO\PaymentStatementParserBundle\Model\SwedbankCsv\SwedbankCsvPaymentClosingBalanceRowModel;
use EMO\PaymentStatementParserBundle\Model\SwedbankCsv\SwedbankCsvPaymentOpeningBalanceRowModel;
use EMO\PaymentStatementParserBundle\Model\SwedbankCsv\SwedbankCsvPaymentTransactionRowModel;
use EMO\PaymentStatementParserBundle\Model\SwedbankCsv\SwedbankCsvPaymentTurnoverRowModel;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * @coversDefaultClass \EMO\PaymentStatementParserBundle\Factory\SwedbankCsv\SwedbankCsvPaymentRowModelFactory
 */
class SwedbankCsvPaymentRowModelFactoryTest extends TestCase
{
    /**
     * @covers ::get
     *
     * @param string[]     $row
     * @param class-string $expectedInstanceClass
     *
     * @dataProvider getCorrectClassProvider
     */
    public function testGetCorrectClass(string $assertMessage, array $row, string $expectedInstanceClass): void
    {
        $swedbankCsvPaymentRowModelFactory = new SwedbankCsvPaymentRowModelFactory();
        $swedbankCsvPaymentRowModel = $swedbankCsvPaymentRowModelFactory->get('line-1', $row, '');
        /** @noinspection UnnecessaryAssertionInspection */
        self::assertInstanceOf($expectedInstanceClass, $swedbankCsvPaymentRowModel, $assertMessage);
    }

    /** @return mixed[][] */
    public function getCorrectClassProvider(): array
    {
        return [
            '0.' => [
                'assertMessage' => 'Opening balance.',
                'row' => [AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_RECORD_TYPE => AbstractSwedbankCsvPaymentRowModel::RECORD_TYPE_OPENING_BALANCE],
                'expectedInstanceClass' => SwedbankCsvPaymentOpeningBalanceRowModel::class,
            ],
            '1.' => [
                'assertMessage' => 'Transaction.',
                'row' => [AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_RECORD_TYPE => AbstractSwedbankCsvPaymentRowModel::RECORD_TYPE_TRANSACTION],
                'expectedInstanceClass' => SwedbankCsvPaymentTransactionRowModel::class,
            ],
            '2.' => [
                'assertMessage' => 'Turnover.',
                'row' => [AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_RECORD_TYPE => AbstractSwedbankCsvPaymentRowModel::RECORD_TYPE_TURNOVER],
                'expectedInstanceClass' => SwedbankCsvPaymentTurnoverRowModel::class,
            ],
            '3.' => [
                'assertMessage' => 'Closing balance.',
                'row' => [AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_RECORD_TYPE => AbstractSwedbankCsvPaymentRowModel::RECORD_TYPE_CLOSING_BALANCE],
                'expectedInstanceClass' => SwedbankCsvPaymentClosingBalanceRowModel::class,
            ],
            '4.' => [
                'assertMessage' => 'Accrued interest.',
                'row' => [AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_RECORD_TYPE => AbstractSwedbankCsvPaymentRowModel::RECORD_TYPE_ACCRUED_INTEREST],
                'expectedInstanceClass' => SwedbankCsvPaymentAccruedInterestRowModel::class,
            ],
        ];
    }

    /**
     * @covers ::get
     *
     * @param string[]                $row
     * @param class-string<Throwable> $expectException
     *
     * @dataProvider getUnsupportedClassProvider
     */
    public function testGetUnsupportedClass(array $row, string $expectedExceptionMessage, string $expectException): void
    {
        $swedbankCsvPaymentRowModelFactory = new SwedbankCsvPaymentRowModelFactory();
        $this->expectExceptionMessage($expectedExceptionMessage);
        $this->expectException($expectException);
        $swedbankCsvPaymentRowModelFactory->get('line-1', $row, '');
    }

    /** @return mixed[][] */
    public function getUnsupportedClassProvider(): array
    {
        return [
            '0. Unsupported record type.' => [
                'row' => [AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_RECORD_TYPE => 'something else'],
                'expectedExceptionMessage' => 'record type "something else" is not implemented.',
                'expectedException' => InvalidStatementContentException::class,
            ],
            '1. No record type.' => [
                'row' => [],
                'expectedExceptionMessage' => 'record type is empty.',
                'expectedException' => InvalidStatementContentException::class,
            ],
            '2. Empty record.' => [
                'row' => [AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_RECORD_TYPE => ''],
                'expectedExceptionMessage' => 'record type is empty.',
                'expectedException' => InvalidStatementContentException::class,
            ],
        ];
    }
}
