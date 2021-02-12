<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Tests\Acceptance\PostLtCsv;

use DateTimeImmutable;
use EMO\PaymentStatementParserBundle\Factory\PostLtCsv\PostLtCsvPaymentRowModelFactory;
use EMO\PaymentStatementParserBundle\Model\PostLtCsv\PostLtCsvPaymentRowFormattedModel;
use EMO\PaymentStatementParserBundle\Service\PostLtCsv\PostLtCsvPaymentDeserializerService;
use EMO\PaymentStatementParserBundle\Service\PostLtCsv\PostLtCsvPaymentValidatorService;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use RuntimeException;
use Symfony\Component\Validator\ConstraintViolationInterface;

use function count;
use function file_get_contents;
use function implode;
use function sprintf;

class ImportPostLtCsvPaymentTest extends WebTestCase
{
    public function test(): void
    {
        $postLtCsvPaymentDeserializerService = $this->getContainer()->get(PostLtCsvPaymentDeserializerService::class);
        $postLtCsvPaymentValidatorService = $this->getContainer()->get(PostLtCsvPaymentValidatorService::class);
        $postLtCsvPaymentRowModelFactory = $this->getContainer()->get(PostLtCsvPaymentRowModelFactory::class);

        $paymentContent = file_get_contents(__DIR__.'/../../../docs/PostLtCsv/paymentImportExamplePostLt.txt');
        $paymentContent = $postLtCsvPaymentDeserializerService->convertEncodingToUtf8($paymentContent);
        $csvRowModels = $postLtCsvPaymentDeserializerService->explodePaymentsCsv($paymentContent);

        $postLtCsvPaymentRowModels = [];
        $violations = [];
        /* create row model */
        foreach ($csvRowModels as $csvRowModel) {
            $lineId = sprintf('line-%d', $csvRowModel->getLineNo());
            $postLtCsvPaymentRowModels[] = $postLtCsvPaymentRowModelFactory->get($lineId, $csvRowModel->getRow(), $csvRowModel->getSource());
        }
        /* validate */
        foreach ($postLtCsvPaymentRowModels as $postLtCsvPaymentRowModel) {
            $constraintViolationList = $postLtCsvPaymentValidatorService->validatePaymentRow($postLtCsvPaymentRowModel);
            if ($constraintViolationList->count() > 0) {
                /** @var ConstraintViolationInterface $constraintViolation */
                foreach ($constraintViolationList as $constraintViolation) {
                    $violations[] = sprintf(
                        '%s %s Value: "%s".',
                        $constraintViolation->getPropertyPath(),
                        $constraintViolation->getMessage(),
                        $constraintViolation->getInvalidValue()
                    );
                }
            }
        }
        if (count($violations)) {
            throw new RuntimeException(sprintf("Must not have validation violations: \n%s", implode("\n", $violations)));
        }
        self::assertCount(2, $postLtCsvPaymentRowModels, '2 rows in the file.');
        /**
         * Get raw csv values from model.
         */
        self::assertSame('Vardenė1 Pavardenis1', $postLtCsvPaymentRowModels[0]->getRawPayedByName(), 'Final model data must be encoded in UTF-8.');
        /**
         * Use helper methods for some of the raw values.
         */
        $postLtCsvPaymentRowFormattedModel = new PostLtCsvPaymentRowFormattedModel($postLtCsvPaymentRowModels[0]);
        self::assertEquals(
            new DateTimeImmutable('2017-09-07'),
            $postLtCsvPaymentRowFormattedModel->getPaymentDateObject(),
            'String must be correctly converted to date time object.'
        );
    }
}
