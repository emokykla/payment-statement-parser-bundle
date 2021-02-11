<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Tests\Unit\Model\SwedbankCsv;

use EMO\PaymentStatementParserBundle\Model\SwedbankCsv\AbstractSwedbankCsvPaymentRowModel;
use EMO\PaymentStatementParserBundle\Model\SwedbankCsv\SwedbankCsvPaymentTransactionRowModel;
use EMO\PaymentStatementParserBundle\Service\SwedbankCsv\SwedbankCsvPaymentValidatorService;
use EMO\PaymentStatementParserBundle\Tests\Service\ViolationUtils;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * @coversDefaultClass \EMO\PaymentStatementParserBundle\Model\SwedbankCsv\SwedbankCsvPaymentTransactionRowModel
 */
class SwedbankCsvPaymentTransactionRowModelTest extends WebTestCase
{
    /**
     * Tests validation rules that are specific to this model.
     *
     * @covers ::loadValidatorMetadata
     *
     * @param string[] $expectedViolations
     *
     * @dataProvider validateProvider
     */
    public function testValidate(string $assertMessage, callable $dataUpdater, array $expectedViolations): void
    {
        /* setup */
        // it's easier to test using validator service because ValidatorInterface is not public to use in tests.
        $swedbankCsvPaymentValidatorService = $this->getContainer()->get(SwedbankCsvPaymentValidatorService::class);
        $validRow = [
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_ACCOUNT_NUMBER => $accountNumber = '$accountNumber',
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_RECORD_TYPE => $recordType = AbstractSwedbankCsvPaymentRowModel::RECORD_TYPE_TRANSACTION,
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
        $swedbankCsvPaymentTransactionRowModel = new SwedbankCsvPaymentTransactionRowModel('line-1', $validRow, '');
        $constraintViolationList = $swedbankCsvPaymentValidatorService->validatePaymentRow($swedbankCsvPaymentTransactionRowModel);
        $violations = ViolationUtils::stringify($constraintViolationList);
        self::assertSame([], $violations, 'Row must be valid before changing data.');
        $swedbankCsvPaymentTransactionRowModel = $dataUpdater($validRow);
        /* do */
        $constraintViolationList = $swedbankCsvPaymentValidatorService->validatePaymentRow($swedbankCsvPaymentTransactionRowModel);
        /* assert */
        $violations = ViolationUtils::stringify($constraintViolationList);
        self::assertSame($expectedViolations, $violations, $assertMessage);
    }

    /** @return mixed[][] */
    public function validateProvider(): array
    {
        return [
            '0.' => [
                'assertMessage' => 'Record type cannot be blank and must be from choice list.',
                'dataUpdater' => static function (array $row): AbstractSwedbankCsvPaymentRowModel {
                    $row[AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_RECORD_TYPE] = '';

                    return new SwedbankCsvPaymentTransactionRowModel('line-1', $row, '');
                },
                'expectedViolations' => [
                    'line-1.recordType [1 column] The value you selected is not a valid choice. Valid choices: "20". Value: "".',
                    'line-1.recordType [1 column] This value should not be blank. Value: "".',
                    'line-1.recordType [1 column] The value you selected is not a valid choice. Valid choices: "10", "20", "82", "86", "900". Value: "".',
                ],
            ],
            '1.' => [
                'assertMessage' => 'Transaction type cannot be blank and must be from choice list.',
                'dataUpdater' => static function (array $row): AbstractSwedbankCsvPaymentRowModel {
                    $row[AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_TRANSACTION_TYPE] = '';

                    return new SwedbankCsvPaymentTransactionRowModel('line-1', $row, '');
                },
                'expectedViolations' => [
                    'line-1.transactionType [9 column] The value you selected is not a valid choice. Valid choices: "MK". Value: "".',
                    'line-1.transactionType [9 column] This value should not be blank. Value: "".',
                    'line-1.transactionType [9 column] The value you selected is not a valid choice. Valid choices: "MK". Value: "".',
                ],
            ],
        ];
    }

    /**
     * @covers ::getAmountInCents
     *
     * @dataProvider getAmountInCentsProvider
     */
    public function testGetAmountInCents(string $amount, int $expectedAmountInCents): void
    {
        /* setup */
        $row = [
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_RECORD_TYPE => $recordType = AbstractSwedbankCsvPaymentRowModel::RECORD_TYPE_TRANSACTION,
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_AMOUNT => $amount,
        ];
        /* do */
        $swedbankCsvPaymentTransactionRowModel = new SwedbankCsvPaymentTransactionRowModel('line-1', $row, '');
        /* assert */
        self::assertSame($expectedAmountInCents, $swedbankCsvPaymentTransactionRowModel->getAmountInCents());
    }

    /** @return mixed[][] */
    public function getAmountInCentsProvider(): array
    {
        return [
            '0.' => [
                'amount' => '0.00',
                'expectedAmountInCents' => 0,
            ],
            '1.' => [
                'amount' => '0.01',
                'expectedAmountInCents' => 1,
            ],
            '2.' => [
                'amount' => '1.00',
                'expectedAmountInCents' => 100,
            ],
            '3.' => [
                'amount' => '12345.67',
                'expectedAmountInCents' => 1234567,
            ],
        ];
    }
}
