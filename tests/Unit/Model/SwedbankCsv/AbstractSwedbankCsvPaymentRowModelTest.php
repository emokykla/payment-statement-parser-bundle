<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Tests\Unit\Model\SwedbankCsv;

use EMO\PaymentStatementParserBundle\Model\SwedbankCsv\AbstractSwedbankCsvPaymentRowModel;
use EMO\PaymentStatementParserBundle\Service\SwedbankCsv\SwedbankCsvPaymentValidatorService;
use EMO\PaymentStatementParserBundle\Tests\Service\ViolationUtils;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * @coversDefaultClass \EMO\PaymentStatementParserBundle\Model\SwedbankCsv\AbstractSwedbankCsvPaymentRowModel
 */
class AbstractSwedbankCsvPaymentRowModelTest extends WebTestCase
{
    /**
     * @covers ::getSourceLineId
     * @covers ::getBankAccountNumber
     * @covers ::getRecordType
     * @covers ::getTransactionDate
     * @covers ::getParty
     * @covers ::getDetails
     * @covers ::getAmount
     * @covers ::getCurrency
     * @covers ::getDebitCreditIndicator
     * @covers ::getTransactionReference
     * @covers ::getTransactionType
     * @covers ::getClientReference
     * @covers ::getDocumentNumber
     */
    public function testGetters(): void
    {
        $row = [
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_BANK_ACCOUNT_NUMBER => $accountNumber = '$accountNumber',
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_RECORD_TYPE => $recordType = AbstractSwedbankCsvPaymentRowModel::RECORD_TYPE_CLOSING_BALANCE,
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_TRANSACTION_DATE => $transactionDate = '$transactionDate',
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_PARTY => $party = '$party',
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_DETAILS => $details = '$details',
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_AMOUNT => $amount = '$amount',
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_CURRENCY => $currency = '$currency',
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_DEBIT_CREDIT_INDICATOR => $debitCreditIndicator = '$debitCreditIndicator',
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_TRANSACTION_REFERENCE => $transactionReference = '$transactionReference',
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_TRANSACTION_TYPE => $transactionType = '$transactionType',
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_CLIENT_REFERENCE => $clientReference = '$clientReference',
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_DOCUMENT_NUMBER => $documentNumber = '$documentNumber',
        ];
        $swedbankCsvPaymentRowModel = new class ($lineId = 'line-1', $row, $sourceString = 'source string') extends AbstractSwedbankCsvPaymentRowModel {
        };
        self::assertSame($lineId, $swedbankCsvPaymentRowModel->getSourceLineId());
        self::assertSame($row, $swedbankCsvPaymentRowModel->getSourceRow());
        self::assertSame($sourceString, $swedbankCsvPaymentRowModel->getSourceString());
        self::assertSame($accountNumber, $swedbankCsvPaymentRowModel->getBankAccountNumber());
        self::assertSame($recordType, $swedbankCsvPaymentRowModel->getRecordType());
        self::assertSame($transactionDate, $swedbankCsvPaymentRowModel->getTransactionDate());
        self::assertSame($party, $swedbankCsvPaymentRowModel->getParty());
        self::assertSame($details, $swedbankCsvPaymentRowModel->getDetails());
        self::assertSame($amount, $swedbankCsvPaymentRowModel->getAmount());
        self::assertSame($currency, $swedbankCsvPaymentRowModel->getCurrency());
        self::assertSame($debitCreditIndicator, $swedbankCsvPaymentRowModel->getDebitCreditIndicator());
        self::assertSame($transactionReference, $swedbankCsvPaymentRowModel->getTransactionReference());
        self::assertSame($transactionType, $swedbankCsvPaymentRowModel->getTransactionType());
        self::assertSame($clientReference, $swedbankCsvPaymentRowModel->getClientReference());
        self::assertSame($documentNumber, $swedbankCsvPaymentRowModel->getDocumentNumber());
    }

    /**
     * Tests validation rules.
     *
     * @covers ::loadValidatorMetadata
     * @covers \EMO\PaymentStatementParserBundle\Service\SwedbankCsv\SwedbankCsvPaymentValidatorService::validatePaymentRow
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
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_BANK_ACCOUNT_NUMBER => $accountNumber = '$accountNumber',
            AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_RECORD_TYPE => $recordType = AbstractSwedbankCsvPaymentRowModel::RECORD_TYPE_CLOSING_BALANCE,
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
        $swedbankCsvPaymentRowModel = new class ('line-1', $validRow, '') extends AbstractSwedbankCsvPaymentRowModel {
        };
        $constraintViolationList = $swedbankCsvPaymentValidatorService->validatePaymentRow($swedbankCsvPaymentRowModel);
        $violations = ViolationUtils::stringify($constraintViolationList);
        self::assertSame([], $violations, 'Row must be valid before changing data.');
        $swedbankCsvPaymentRowModel = $dataUpdater($validRow);
        /* do */
        $constraintViolationList = $swedbankCsvPaymentValidatorService->validatePaymentRow($swedbankCsvPaymentRowModel);
        /* assert */
        $violations = ViolationUtils::stringify($constraintViolationList);
        self::assertSame($expectedViolations, $violations, $assertMessage);
    }

    /** @return mixed[][] */
    public function validateProvider(): array
    {
        return [
            '0.' => [
                'assertMessage' => 'Incorrect column count.',
                'dataUpdater' => static function (array $row): AbstractSwedbankCsvPaymentRowModel {
                    $row[] = '';

                    return new class ('line-1', $row, '') extends AbstractSwedbankCsvPaymentRowModel {
                    };
                },
                'expectedViolations' => [
                    'line-1.sourceRow This collection should contain exactly 12 elements. Value: "Array".',
                ],
            ],
            '1.' => [
                'assertMessage' => 'Account number cannot be blank.',
                'dataUpdater' => static function (array $row): AbstractSwedbankCsvPaymentRowModel {
                    $row[AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_BANK_ACCOUNT_NUMBER] = '';

                    return new class ('line-1', $row, '') extends AbstractSwedbankCsvPaymentRowModel {
                    };
                },
                'expectedViolations' => ['line-1.bankAccountNumber [0 column] This value should not be blank. Value: "".'],
            ],
            '2.' => [
                'assertMessage' => 'Record type cannot be blank and must be from choice list.',
                'dataUpdater' => static function (array $row): AbstractSwedbankCsvPaymentRowModel {
                    $row[AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_RECORD_TYPE] = '';

                    return new class ('line-1', $row, '') extends AbstractSwedbankCsvPaymentRowModel {
                    };
                },
                'expectedViolations' => [
                    'line-1.recordType [1 column] This value should not be blank. Value: "".',
                    'line-1.recordType [1 column] The value you selected is not a valid choice. Valid choices: "10", "20", "82", "86", "900". Value: "".',
                ],
            ],
            '3.' => [
                'assertMessage' => 'Transaction date cannot be blank.',
                'dataUpdater' => static function (array $row): AbstractSwedbankCsvPaymentRowModel {
                    $row[AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_TRANSACTION_DATE] = '';

                    return new class ('line-1', $row, '') extends AbstractSwedbankCsvPaymentRowModel {
                    };
                },
                'expectedViolations' => [
                    'line-1.transactionDate [2 column] This value should not be blank. Value: "".',
                ],
            ],
            '4.' => [
                'assertMessage' => 'Transaction date must be in correct format (yyyy-mm-dd).',
                'dataUpdater' => static function (array $row): AbstractSwedbankCsvPaymentRowModel {
                    $row[AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_TRANSACTION_DATE] = '2011/12/13';

                    return new class ('line-1', $row, '') extends AbstractSwedbankCsvPaymentRowModel {
                    };
                },
                'expectedViolations' => [
                    'line-1.transactionDate [2 column] This value is not valid. Valid formats: "yyyy-mm-dd". Value: "2011/12/13".',
                ],
            ],
            '5.' => [
                'assertMessage' => 'Payment date cannot have trailing characters.',
                'dataUpdater' => static function (array $row): AbstractSwedbankCsvPaymentRowModel {
                    $row[AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_TRANSACTION_DATE] = '-2011-12-13 00:00';

                    return new class ('line-1', $row, '') extends AbstractSwedbankCsvPaymentRowModel {
                    };
                },
                'expectedViolations' => [
                    'line-1.transactionDate [2 column] This value is not valid. Valid formats: "yyyy-mm-dd". Value: "-2011-12-13 00:00".',
                ],
            ],
            '6.' => [
                'assertMessage' => 'Party cannot be blank.',
                'dataUpdater' => static function (array $row): AbstractSwedbankCsvPaymentRowModel {
                    $row[AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_PARTY] = '';

                    return new class ('line-1', $row, '') extends AbstractSwedbankCsvPaymentRowModel {
                    };
                },
                'expectedViolations' => [
                    'line-1.party [3 column] This value should not be blank. Value: "".',
                ],
            ],
            '7.' => [
                'assertMessage' => 'Details cannot be blank.',
                'dataUpdater' => static function (array $row): AbstractSwedbankCsvPaymentRowModel {
                    $row[AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_DETAILS] = '';

                    return new class ('line-1', $row, '') extends AbstractSwedbankCsvPaymentRowModel {
                    };
                },
                'expectedViolations' => [
                    'line-1.details [4 column] This value should not be blank. Value: "".',
                ],
            ],
            '8.' => [
                'assertMessage' => 'Amount cannot be blank and must be number greater than zero.',
                'dataUpdater' => static function (array $row): AbstractSwedbankCsvPaymentRowModel {
                    $row[AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_AMOUNT] = '';

                    return new class ('line-1', $row, '') extends AbstractSwedbankCsvPaymentRowModel {
                    };
                },
                'expectedViolations' => [
                    'line-1.amount [5 column] This value should not be blank. Value: "".',
                ],
            ],
            '9.' => [
                'assertMessage' => 'Amount must be formatted as float.',
                'dataUpdater' => static function (array $row): AbstractSwedbankCsvPaymentRowModel {
                    $row[AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_AMOUNT] = '1';

                    return new class ('line-1', $row, '') extends AbstractSwedbankCsvPaymentRowModel {
                    };
                },
                'expectedViolations' => [
                    'line-1.amount [5 column] Value must be formatted as float "x.yy". Value: "1".',
                ],
            ],
            '10.' => [
                'assertMessage' => 'Amount must be positive.',
                'dataUpdater' => static function (array $row): AbstractSwedbankCsvPaymentRowModel {
                    $row[AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_AMOUNT] = '-1.00';

                    return new class ('line-1', $row, '') extends AbstractSwedbankCsvPaymentRowModel {
                    };
                },
                'expectedViolations' => [
                    'line-1.amount [5 column] Value must be formatted as float "x.yy". Value: "-1.00".',
                ],
            ],
            '11.' => [
                'assertMessage' => 'Currency cannot be blank and must be from supported currency list.',
                'dataUpdater' => static function (array $row): AbstractSwedbankCsvPaymentRowModel {
                    $row[AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_CURRENCY] = '';

                    return new class ('line-1', $row, '') extends AbstractSwedbankCsvPaymentRowModel {
                    };
                },
                'expectedViolations' => [
                    'line-1.currency [6 column] This value should not be blank. Value: "".',
                    'line-1.currency [6 column] The value you selected is not a valid choice. Valid choices: "EUR". Value: "".',
                ],
            ],
            '12.' => [
                'assertMessage' => 'Debit-credit indicator cannot be blank and must be from choice list.',
                'dataUpdater' => static function (array $row): AbstractSwedbankCsvPaymentRowModel {
                    $row[AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_DEBIT_CREDIT_INDICATOR] = '';

                    return new class ('line-1', $row, '') extends AbstractSwedbankCsvPaymentRowModel {
                    };
                },
                'expectedViolations' => [
                    'line-1.debitCreditIndicator [7 column] This value should not be blank. Value: "".',
                    'line-1.debitCreditIndicator [7 column] The value you selected is not a valid choice. Valid choices: "K", "D". Value: "".',
                ],
            ],
            '13.' => [
                'assertMessage' => 'Transaction reference cannot be blank.',
                'dataUpdater' => static function (array $row): AbstractSwedbankCsvPaymentRowModel {
                    $row[AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_TRANSACTION_REFERENCE] = '';

                    return new class ('line-1', $row, '') extends AbstractSwedbankCsvPaymentRowModel {
                    };
                },
                'expectedViolations' => [
                    'line-1.transactionReference [8 column] This value should not be blank. Value: "".',
                ],
            ],
            '14.' => [
                'assertMessage' => 'Transaction reference must be formatted as 16 digits.',
                'dataUpdater' => static function (array $row): AbstractSwedbankCsvPaymentRowModel {
                    $row[AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_TRANSACTION_REFERENCE] = '1';

                    return new class ('line-1', $row, '') extends AbstractSwedbankCsvPaymentRowModel {
                    };
                },
                'expectedViolations' => [
                    'line-1.transactionReference [8 column] Value must be formatted as 16 digits. Value: "1".',
                ],
            ],
            '15.' => [
                'assertMessage' => 'Transaction reference must be without any preceding or trailing symbols.',
                'dataUpdater' => static function (array $row): AbstractSwedbankCsvPaymentRowModel {
                    $row[AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_TRANSACTION_REFERENCE] = '-1234567890123456';

                    return new class ('line-1', $row, '') extends AbstractSwedbankCsvPaymentRowModel {
                    };
                },
                'expectedViolations' => [
                    'line-1.transactionReference [8 column] Value must be formatted as 16 digits. Value: "-1234567890123456".',
                ],
            ],
            '16.' => [
                'assertMessage' => 'Transaction type cannot be blank and must be from choice list.',
                'dataUpdater' => static function (array $row): AbstractSwedbankCsvPaymentRowModel {
                    $row[AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_TRANSACTION_TYPE] = '';

                    return new class ('line-1', $row, '') extends AbstractSwedbankCsvPaymentRowModel {
                    };
                },
                'expectedViolations' => [
                    'line-1.transactionType [9 column] This value should not be blank. Value: "".',
                    'line-1.transactionType [9 column] The value you selected is not a valid choice. Valid choices: "MK". Value: "".',
                ],
            ],
            '17.' => [
                'assertMessage' => 'Client reference must be blank.',
                'dataUpdater' => static function (array $row): AbstractSwedbankCsvPaymentRowModel {
                    $row[AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_CLIENT_REFERENCE] = 'test';

                    return new class ('line-1', $row, '') extends AbstractSwedbankCsvPaymentRowModel {
                    };
                },
                'expectedViolations' => [
                    'line-1.clientReference [10 column] This value should be blank, documentation says "Not used". Value: "test".',
                ],
            ],
            '18.' => [
                'assertMessage' => 'Document number accepts blank.',
                'dataUpdater' => static function (array $row): AbstractSwedbankCsvPaymentRowModel {
                    $row[AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_DOCUMENT_NUMBER] = '';

                    return new class ('line-1', $row, '') extends AbstractSwedbankCsvPaymentRowModel {
                    };
                },
                'expectedViolations' => [],
            ],
            '19.' => [
                'assertMessage' => 'Document number accepts any string.',
                'dataUpdater' => static function (array $row): AbstractSwedbankCsvPaymentRowModel {
                    $row[AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_DOCUMENT_NUMBER] = 'test';

                    return new class ('line-1', $row, '') extends AbstractSwedbankCsvPaymentRowModel {
                    };
                },
                'expectedViolations' => [],
            ],
        ];
    }
}
