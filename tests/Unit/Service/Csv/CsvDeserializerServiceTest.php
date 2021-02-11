<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Tests\Unit\Service\Csv;

use EMO\PaymentStatementParserBundle\Service\Csv\CsvDeserializerService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

/**
 * @coversDefaultClass \EMO\PaymentStatementParserBundle\Service\Csv\CsvDeserializerService
 */
class CsvDeserializerServiceTest extends TestCase
{
    /**
     * @covers ::explodePaymentsCsv
     */
    public function testExplodePaymentsCsv(): void
    {
        $csvDeserializerService = new CsvDeserializerService();
        $encoderSettings = [
            CsvEncoder::DELIMITER_KEY => ',',
            CsvEncoder::ENCLOSURE_KEY => '"',
            CsvEncoder::KEY_SEPARATOR_KEY => '.',
            CsvEncoder::NO_HEADERS_KEY => true,
            CsvEncoder::AS_COLLECTION_KEY => true,
        ];
        $csvRowModels = $csvDeserializerService->explodePaymentsCsv(
            <<<'CSV'
1-first,"2-second with
new line"
"2-first","""2-s,e,c,o,n,d"""
CSV
            ,
            $encoderSettings
        );
        self::assertCount(2, $csvRowModels, 'Csv must be split correctly and must have 2 rows.');
        /* first line */
        self::assertSame(1, $csvRowModels[0]->getLineNo(), 'Lines must numbered from 1.');
        self::assertSame(['1-first', "2-second with\nnew line"], $csvRowModels[0]->getRow(), 'Columns must be split correctly and value include new line.');
        self::assertSame("1-first,\"2-second with\nnew line\"", $csvRowModels[0]->getSource(), 'Source line must be reassembled correctly including new line.');
        /* second line */
        self::assertSame(2, $csvRowModels[1]->getLineNo(), 'Line number must be 2 even though previous line has new line in column value.');
        self::assertSame(['2-first', '"2-s,e,c,o,n,d"'], $csvRowModels[1]->getRow(), 'Columns must be split correctly even though value includes commas.');
        self::assertSame('2-first,"""2-s,e,c,o,n,d"""', $csvRowModels[1]->getSource(), 'Source line must be reassembled correctly including quote symbols.');
    }
}
