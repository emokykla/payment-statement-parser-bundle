<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Tests\Unit\Service\PostLtCsv;

use EMO\PaymentStatementParserBundle\Service\Csv\CsvDeserializerService;
use EMO\PaymentStatementParserBundle\Service\PostLtCsv\PostLtCsvPaymentDeserializerService;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \EMO\PaymentStatementParserBundle\Service\PostLtCsv\PostLtCsvPaymentDeserializerService
 */
class PostLtCsvPaymentDeserializerServiceTest extends TestCase
{
    /**
     * @covers ::convertEncodingToUtf8
     */
    public function testConvertEncodingToUtf8(): void
    {
        $postLtCsvPaymentDeserializerService = new PostLtCsvPaymentDeserializerService(new CsvDeserializerService());
        $expectedString = 'ąčęėįšųū()-ž';
        $testString = mb_convert_encoding($expectedString, 'iso-8859-13', 'utf-8');
        self::assertSame($expectedString, $postLtCsvPaymentDeserializerService->convertEncodingToUtf8($testString));
    }
}
