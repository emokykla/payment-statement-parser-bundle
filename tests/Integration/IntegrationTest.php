<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Tests\Integration;

use EMO\PaymentStatementParserBundle\Service\DummyService;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class IntegrationTest extends WebTestCase
{
    public function test(): void
    {
        $dummyService = $this->getContainer()->get(DummyService::class);
        self::assertSame('asd', $dummyService->getString());
    }
}
