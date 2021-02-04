<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Tests\Acceptance\SwedbankCsv;

use EMO\PaymentStatementParserBundle\Factory\SwedbankCsv\SwedbankCsvPaymentRowModelFactory;
use EMO\PaymentStatementParserBundle\Model\SwedbankCsv\AbstractSwedbankCsvPaymentRowModel;
use EMO\PaymentStatementParserBundle\Model\SwedbankCsv\SwedbankCsvPaymentTransactionRowModel;
use EMO\PaymentStatementParserBundle\Service\SwedbankCsv\SwedbankCsvPaymentDeserializerService;
use EMO\PaymentStatementParserBundle\Service\SwedbankCsv\SwedbankCsvPaymentValidatorService;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use RuntimeException;
use Symfony\Component\Validator\ConstraintViolationInterface;

use function array_filter;
use function count;
use function file_get_contents;
use function implode;
use function sprintf;

class ImportSwedbankCsvPaymentTest extends WebTestCase
{
    public function test(): void
    {
        $swedbankCsvPaymentDeserializerService = $this->getContainer()->get(SwedbankCsvPaymentDeserializerService::class);
        $swedbankLtPaymentValidatorService = $this->getContainer()->get(SwedbankCsvPaymentValidatorService::class);
        $swedbankLtPaymentRowModelFactory = $this->getContainer()->get(SwedbankCsvPaymentRowModelFactory::class);

        $paymentContent = file_get_contents(__DIR__.'/../../../docs/SwedbankCsv/paymentImportExampleSwedbank.csv');
        $paymentContent = mb_convert_encoding($paymentContent, 'utf-8', 'iso-8859-13');
        $paymentRows = $swedbankCsvPaymentDeserializerService->explodePaymentsCsv($paymentContent);

        $swedbankCsvPaymentRowModels = [];
        $violations = [];
        /* create row model */
        foreach ($paymentRows as $rowKey => $paymentRow) {
            $lineId = sprintf('line-%d', $rowKey + 1);
            $swedbankCsvPaymentRowModels[] = $swedbankLtPaymentRowModelFactory->get($lineId, $paymentRow);
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
            $constraintViolationList = $swedbankLtPaymentValidatorService->validatePaymentRow($transactionModel);
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
        self::assertGreaterThan(0, count($transactionModels));
    }
}
