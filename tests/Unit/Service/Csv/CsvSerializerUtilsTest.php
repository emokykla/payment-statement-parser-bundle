<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Tests\Unit\Service\Csv;

use EMO\PaymentStatementParserBundle\Service\Csv\CsvSerializerUtils;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \EMO\PaymentStatementParserBundle\Service\Csv\CsvSerializerUtils
 */
class CsvSerializerUtilsTest extends TestCase
{
    /**
     * @covers ::ensureArrayOfArrays
     */
    public function testEnsureArrayOfArrays(): void
    {
        $rows = [['first', 'second']];
        $fixedRows = CsvSerializerUtils::ensureArrayOfArrays($rows);
        self::assertSame($rows, $fixedRows, 'Rows array contains arrays, must leave unchanged.');

        $rows = ['first', 'second'];
        $fixedRows = CsvSerializerUtils::ensureArrayOfArrays($rows);
        self::assertSame([$rows], $fixedRows, 'Rows array has only strings, must convert to array of arrays.');
    }
}
