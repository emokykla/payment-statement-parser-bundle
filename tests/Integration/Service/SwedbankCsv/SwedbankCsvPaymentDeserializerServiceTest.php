<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Tests\Integration\Service\SwedbankCsv;

use EMO\PaymentStatementParserBundle\Service\SwedbankCsv\SwedbankCsvPaymentDeserializerService;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * @coversDefaultClass \EMO\PaymentStatementParserBundle\Service\SwedbankCsv\SwedbankCsvPaymentDeserializerService
 */
class SwedbankCsvPaymentDeserializerServiceTest extends WebTestCase
{
    /**
     * @covers ::explodePaymentsCsv
     */
    public function testExplodePaymentsCsv(): void
    {
        $swedbankCsvPaymentDeserializerService = $this->getContainer()->get(SwedbankCsvPaymentDeserializerService::class);
        $rows = $swedbankCsvPaymentDeserializerService->explodePaymentsCsv('"first","second","""t,h,i,r,d"""');
        self::assertSame([['first', 'second', '"t,h,i,r,d"']], $rows, 'Must be array of arrays of strings with 3 elements.');
    }
}
