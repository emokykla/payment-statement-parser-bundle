<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Tests\Acceptance\SwedbankCsv;

use DateTimeImmutable;
use EMO\PaymentStatementParserBundle\Factory\SwedbankCsv\SwedbankCsvPaymentRowModelFactory;
use EMO\PaymentStatementParserBundle\Model\SwedbankCsv\AbstractSwedbankCsvPaymentRowModel;
use EMO\PaymentStatementParserBundle\Model\SwedbankCsv\SwedbankCsvPaymentTransactionFormattedRowModel;
use EMO\PaymentStatementParserBundle\Model\SwedbankCsv\SwedbankCsvPaymentTransactionRowModel;
use EMO\PaymentStatementParserBundle\Service\SwedbankCsv\SwedbankCsvPaymentDeserializerService;
use EMO\PaymentStatementParserBundle\Service\SwedbankCsv\SwedbankCsvPaymentValidatorService;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use RuntimeException;
use Symfony\Component\Validator\ConstraintViolationInterface;

use function array_filter;
use function array_values;
use function count;
use function file_get_contents;
use function implode;
use function sprintf;

class ImportSwedbankCsvPaymentTest extends WebTestCase
{
    public function test(): void
    {
        $swedbankCsvPaymentDeserializerService = $this->getContainer()->get(SwedbankCsvPaymentDeserializerService::class);
        $swedbankCsvPaymentValidatorService = $this->getContainer()->get(SwedbankCsvPaymentValidatorService::class);
        $swedbankCsvPaymentRowModelFactory = $this->getContainer()->get(SwedbankCsvPaymentRowModelFactory::class);

        $paymentContent = file_get_contents(__DIR__.'/../../../docs/SwedbankCsv/paymentImportExampleSwedbank.csv');
        $paymentContent = $swedbankCsvPaymentDeserializerService->convertEncodingToUtf8($paymentContent);
        $csvRowModels = $swedbankCsvPaymentDeserializerService->explodePaymentsCsv($paymentContent);

        $swedbankCsvPaymentRowModels = [];
        $violations = [];
        /* create row model */
        foreach ($csvRowModels as $csvRowModel) {
            $lineId = sprintf('line-%d', $csvRowModel->getLineNo());
            $swedbankCsvPaymentRowModels[] = $swedbankCsvPaymentRowModelFactory->get($lineId, $csvRowModel->getRow(), $csvRowModel->getSource());
        }
        /* filter what you need, most likely - transactions */
        /** @var SwedbankCsvPaymentTransactionRowModel[] $transactionModels */
        $transactionModels = array_filter(
            $swedbankCsvPaymentRowModels,
            static function (AbstractSwedbankCsvPaymentRowModel $swedbankCsvPaymentRowModel): bool {
                return $swedbankCsvPaymentRowModel instanceof SwedbankCsvPaymentTransactionRowModel;
            }
        );
        /* validate */
        foreach ($transactionModels as $transactionModel) {
            $constraintViolationList = $swedbankCsvPaymentValidatorService->validatePaymentRow($transactionModel);
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
        /* keep only credit transactions */
        $transactionModels = array_filter(
            $transactionModels,
            static function (SwedbankCsvPaymentTransactionRowModel $csvPaymentTransactionRowModel): bool {
                return $csvPaymentTransactionRowModel->getDebitCreditIndicator() === SwedbankCsvPaymentTransactionRowModel::INDICATOR_CREDIT;
            }
        );
        self::assertCount(9, $transactionModels, 'There are 9 transaction lines in file.');
        self::assertSame(
            'Vardenė1 Pavardenis1 | AGBLLT2XXXX | LT154010042403333333',
            array_values($transactionModels)[0]->getParty(),
            'Final model data must be encoded in UTF-8.'
        );
        $swedbankCsvPaymentTransactionFormattedRowModel = new SwedbankCsvPaymentTransactionFormattedRowModel(array_values($transactionModels)[1]);
        self::assertSame(28350, $swedbankCsvPaymentTransactionFormattedRowModel->getAmountInCents());
        self::assertEquals(new DateTimeImmutable('2017-09-04 00:00:00'), $swedbankCsvPaymentTransactionFormattedRowModel->getTransactionDate());
        self::assertSame('XXX 3333333', $swedbankCsvPaymentTransactionFormattedRowModel->getDetailsModel()->getPurposeOfPayment());
    }
}
