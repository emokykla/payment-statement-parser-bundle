<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Tests\Unit\Service\SwedbankCsv;

use EMO\PaymentStatementParserBundle\Service\Csv\CsvDeserializerService;
use EMO\PaymentStatementParserBundle\Service\SwedbankCsv\SwedbankCsvPaymentDeserializerService;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \EMO\PaymentStatementParserBundle\Service\SwedbankCsv\SwedbankCsvPaymentDeserializerService
 */
class SwedbankCsvPaymentDeserializerServiceTest extends TestCase
{
    /**
     * @covers ::convertEncodingToUtf8
     */
    public function testConvertEncodingToUtf8(): void
    {
        $swedbankCsvPaymentDeserializerService = new SwedbankCsvPaymentDeserializerService(new CsvDeserializerService());
        $expectedString = 'ąčęėįšųū()-ž';
        $testString = mb_convert_encoding($expectedString, 'iso-8859-13', 'utf-8');
        self::assertSame($expectedString, $swedbankCsvPaymentDeserializerService->convertEncodingToUtf8($testString));
    }
}
