<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Tests\Unit\Model\SwedbankCsv;

use EMO\PaymentStatementParserBundle\Model\SwedbankCsv\AbstractSwedbankCsvPaymentRowModel;
use EMO\PaymentStatementParserBundle\Model\SwedbankCsv\SwedbankCsvPaymentTurnoverRowModel;
use EMO\PaymentStatementParserBundle\Service\SwedbankCsv\SwedbankCsvPaymentValidatorService;
use EMO\PaymentStatementParserBundle\Tests\Service\ViolationUtils;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * @coversDefaultClass \EMO\PaymentStatementParserBundle\Model\SwedbankCsv\SwedbankCsvPaymentTurnoverRowModel
 */
class SwedbankCsvPaymentTurnoverRowModelTest extends WebTestCase
{
    /**
     * Tests validation rules that are specific to this model.
     *
     * @covers ::loadValidatorMetadata
     */
    public function testValidate(): void
    {
        /* setup */
        // it's easier to test using validator service because ValidatorInterface is not public to use in tests.
        $swedbankCsvPaymentValidatorService = $this->getContainer()->get(SwedbankCsvPaymentValidatorService::class);
        $validRow = [
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_BANK_ACCOUNT_NUMBER => $accountNumber = '$accountNumber',
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_RECORD_TYPE => $recordType = AbstractSwedbankCsvPaymentRowModel::RECORD_TYPE_TURNOVER,
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_TRANSACTION_DATE => $transactionDate = '2011-12-13',
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_PARTY => $party = '$party',
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_DETAILS => $details = '$details',
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_AMOUNT => $amount = '1.00',
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_CURRENCY => $currency = AbstractSwedbankCsvPaymentRowModel::CURRENCY_EUR,
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_DEBIT_CREDIT_INDICATOR => $debitCreditIndicator = AbstractSwedbankCsvPaymentRowModel::INDICATOR_CREDIT,
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_TRANSACTION_REFERENCE => $transactionReference = '1234567890123456',
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_TRANSACTION_TYPE => $transactionType = AbstractSwedbankCsvPaymentRowModel::TRANSACTION_TYPE_MK,
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_CLIENT_REFERENCE => $clientReference = '',
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_DOCUMENT_NUMBER => $documentNumber = '$documentNumber',
        ];
        $swedbankCsvPaymentTurnoverRowModel = new SwedbankCsvPaymentTurnoverRowModel('line-1', $validRow, '');
        $constraintViolationList = $swedbankCsvPaymentValidatorService->validatePaymentRow($swedbankCsvPaymentTurnoverRowModel);
        $violations = ViolationUtils::stringify($constraintViolationList);
        self::assertSame(
            [
                'line-1 Validation for "EMO\PaymentStatementParserBundle\Model\SwedbankCsv\SwedbankCsvPaymentTurnoverRowModel" is not implemented. '.
                'Add it when the need arises. Value: "object of "EMO\PaymentStatementParserBundle\Model\SwedbankCsv\SwedbankCsvPaymentTurnoverRowModel" class".',
            ],
            $violations,
            'Validation is not implemented yet.'
        );
    }
}
