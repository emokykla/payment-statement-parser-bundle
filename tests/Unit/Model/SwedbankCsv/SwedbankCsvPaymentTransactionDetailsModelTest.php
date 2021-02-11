<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Tests\Unit\Model\SwedbankCsv;

use EMO\PaymentStatementParserBundle\Model\SwedbankCsv\SwedbankCsvPaymentTransactionDetailsModel;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \EMO\PaymentStatementParserBundle\Model\SwedbankCsv\SwedbankCsvPaymentTransactionDetailsModel
 */
class SwedbankCsvPaymentTransactionDetailsModelTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testInvalidData(): void
    {
        $this->expectExceptionMessage('Details string was not in correct format, expected 9 strings concatenated by "/", got "invalid data".');
        new SwedbankCsvPaymentTransactionDetailsModel('invalid data');
    }

    /**
     * @covers ::getPurposeOfPayment
     */
    public function testGetPurposeOfPayment(): void
    {
        $swedbankCsvPaymentTransactionDetailsModel = new SwedbankCsvPaymentTransactionDetailsModel("purpose \nof payment/ / / \n / / / / / ");
        self::assertSame("purpose \nof payment", $swedbankCsvPaymentTransactionDetailsModel->getPurposeOfPayment());
    }
}
