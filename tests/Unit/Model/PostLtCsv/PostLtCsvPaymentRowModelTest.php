<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Tests\Unit\Model\PostLtCsv;

use DateTimeImmutable;
use EMO\PaymentStatementParserBundle\Model\PostLtCsv\PostLtCsvPaymentRowModel;
use EMO\PaymentStatementParserBundle\Service\PostLtCsv\PostLtCsvPaymentValidatorService;
use EMO\PaymentStatementParserBundle\Tests\Service\ViolationUtils;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * @coversDefaultClass \EMO\PaymentStatementParserBundle\Model\PostLtCsv\PostLtCsvPaymentRowModel
 */
class PostLtCsvPaymentRowModelTest extends WebTestCase
{
    /**
     * @covers ::getLineId
     * @covers ::getAccountNumber
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
            PostLtCsvPaymentRowModel::INPUT_KEY_POST_CODE => $postCode = '$postCode',
            PostLtCsvPaymentRowModel::INPUT_KEY_PAYMENT_DATE => $paymentDate = '$paymentDate',
            PostLtCsvPaymentRowModel::INPUT_KEY_BANK_ACCOUNT_NUMBER => $bankAccountNumber = '$bankAccountNumber',
            PostLtCsvPaymentRowModel::INPUT_KEY_PAYMENT_DETAILS => $paymentDetails = '$paymentDetails',
            PostLtCsvPaymentRowModel::INPUT_KEY_ADDITIONAL_INFORMATION => $additionalInformation = '$additionalInformation',
            PostLtCsvPaymentRowModel::INPUT_KEY_PAYED_BY_NAME => $payedByName = '$payedByName',
            PostLtCsvPaymentRowModel::INPUT_KEY_PAYED_BY_ADDRESS => $payedByAddress = '$payedByAddress',
            PostLtCsvPaymentRowModel::INPUT_KEY_PAYMENT_CODE => $paymentCode = '$paymentCode',
            PostLtCsvPaymentRowModel::INPUT_KEY_AMOUNT => $amount = '$amount',
            PostLtCsvPaymentRowModel::INPUT_KEY_CURRENCY => $currency = '$currency',
            PostLtCsvPaymentRowModel::INPUT_KEY_BANK_TRANSFER_CODE => $bankTransferCode = '$bankTransferCode',
            PostLtCsvPaymentRowModel::INPUT_KEY_BANK_TRANSFER_DATE => $bankTransferDate = '$bankTransferDate',
        ];
        $postLtCsvPaymentRowModel = new PostLtCsvPaymentRowModel($lineId = 'line-1', $row, $sourceString = 'source string');
        self::assertSame($lineId, $postLtCsvPaymentRowModel->getLineId());
        self::assertSame($row, $postLtCsvPaymentRowModel->getSourceRow());
        self::assertSame($postCode, $postLtCsvPaymentRowModel->getPostCode());
        self::assertSame($paymentDate, $postLtCsvPaymentRowModel->getPaymentDate());
        self::assertSame($bankAccountNumber, $postLtCsvPaymentRowModel->getBankAccountNumber());
        self::assertSame($paymentDetails, $postLtCsvPaymentRowModel->getPaymentDetails());
        self::assertSame($additionalInformation, $postLtCsvPaymentRowModel->getAdditionalInformation());
        self::assertSame($payedByName, $postLtCsvPaymentRowModel->getPayedByName());
        self::assertSame($payedByAddress, $postLtCsvPaymentRowModel->getPayedByAddress());
        self::assertSame($paymentCode, $postLtCsvPaymentRowModel->getPaymentCode());
        self::assertSame($amount, $postLtCsvPaymentRowModel->getAmount());
        self::assertSame($currency, $postLtCsvPaymentRowModel->getCurrency());
        self::assertSame($bankTransferCode, $postLtCsvPaymentRowModel->getBankTransferCode());
        self::assertSame($bankTransferDate, $postLtCsvPaymentRowModel->getBankTransferDate());
    }

    /**
     * Tests validation rules.
     *
     * @covers ::loadValidatorMetadata
     * @covers       \EMO\PaymentStatementParserBundle\Service\PostLtCsv\PostLtCsvPaymentValidatorService::validatePaymentRow
     *
     * @param string[] $expectedViolations
     *
     * @dataProvider validateProvider
     */
    public function testValidate(string $assertMessage, callable $dataUpdater, array $expectedViolations): void
    {
        /* setup */
        // it's easier to test using validator service because ValidatorInterface is not public to use in tests.
        $postLtCsvPaymentValidatorService = $this->getContainer()->get(PostLtCsvPaymentValidatorService::class);
        $validRow = [
            PostLtCsvPaymentRowModel::INPUT_KEY_POST_CODE => $postCode = '01010',
            PostLtCsvPaymentRowModel::INPUT_KEY_PAYMENT_DATE => $paymentDate = '2018.01.01',
            PostLtCsvPaymentRowModel::INPUT_KEY_BANK_ACCOUNT_NUMBER => $bankAccountNumber = 'LT357300010133333333',
            PostLtCsvPaymentRowModel::INPUT_KEY_PAYMENT_DETAILS => $paymentDetails = '$paymentDetails',
            PostLtCsvPaymentRowModel::INPUT_KEY_ADDITIONAL_INFORMATION => $additionalInformation = '$additionalInformation',
            PostLtCsvPaymentRowModel::INPUT_KEY_PAYED_BY_NAME => $payedByName = '$payedByName',
            PostLtCsvPaymentRowModel::INPUT_KEY_PAYED_BY_ADDRESS => $payedByAddress = '$payedByAddress',
            PostLtCsvPaymentRowModel::INPUT_KEY_PAYMENT_CODE => $paymentCode = '123',
            PostLtCsvPaymentRowModel::INPUT_KEY_AMOUNT => $amount = '1.00',
            PostLtCsvPaymentRowModel::INPUT_KEY_CURRENCY => $currency = PostLtCsvPaymentRowModel::CURRENCY_EUR,
            PostLtCsvPaymentRowModel::INPUT_KEY_BANK_TRANSFER_CODE => $bankTransferCode = '2566074',
            PostLtCsvPaymentRowModel::INPUT_KEY_BANK_TRANSFER_DATE => $bankTransferDate = '2018.01.01',
        ];
        // add empty rows at the end to simulate empty "Counter" columns
        for ($index = 0; $index < 12; $index++) {
            $validRow[] = '';
        }
        $postLtCsvPaymentRowModel = new PostLtCsvPaymentRowModel($lineId = 'line-1', $validRow, $sourceString = 'source string');
        $constraintViolationList = $postLtCsvPaymentValidatorService->validatePaymentRow($postLtCsvPaymentRowModel);
        $violations = ViolationUtils::stringify($constraintViolationList);
        self::assertSame([], $violations, 'Row must be valid before changing data.');
        $postLtCsvPaymentRowModel = $dataUpdater($validRow);
        /* do */
        $constraintViolationList = $postLtCsvPaymentValidatorService->validatePaymentRow($postLtCsvPaymentRowModel);
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
                'dataUpdater' => static function (array $row): PostLtCsvPaymentRowModel {
                    $row[] = '';

                    return new PostLtCsvPaymentRowModel('line-1', $row, '');
                },
                'expectedViolations' => [
                    'line-1.sourceRow This collection should contain exactly 24 elements. Value: "Array".',
                ],
            ],
            '1.' => [
                'assertMessage' => 'Post code cannot be blank.',
                'dataUpdater' => static function (array $row): PostLtCsvPaymentRowModel {
                    $row[PostLtCsvPaymentRowModel::INPUT_KEY_POST_CODE] = '';

                    return new PostLtCsvPaymentRowModel('line-1', $row, '');
                },
                'expectedViolations' => [
                    'line-1.postCode [0 column] This value should not be blank. Value: "".',
                    'line-1.postCode [0 column] This value should be of type digit. Value: "".',
                ],
            ],
            '2.' => [
                'assertMessage' => 'Post code must be integer.',
                'dataUpdater' => static function (array $row): PostLtCsvPaymentRowModel {
                    $row[PostLtCsvPaymentRowModel::INPUT_KEY_POST_CODE] = '99abc';

                    return new PostLtCsvPaymentRowModel('line-1', $row, '');
                },
                'expectedViolations' => ['line-1.postCode [0 column] This value should be of type digit. Value: "99abc".'],
            ],
            '3.' => [
                'assertMessage' => 'Post code must be positive.',
                'dataUpdater' => static function (array $row): PostLtCsvPaymentRowModel {
                    $row[PostLtCsvPaymentRowModel::INPUT_KEY_POST_CODE] = '-1';

                    return new PostLtCsvPaymentRowModel('line-1', $row, '');
                },
                'expectedViolations' => ['line-1.postCode [0 column] This value should be of type digit. Value: "-1".'],
            ],
            '4.' => [
                'assertMessage' => 'Payment date cannot be blank.',
                'dataUpdater' => static function (array $row): PostLtCsvPaymentRowModel {
                    $row[PostLtCsvPaymentRowModel::INPUT_KEY_PAYMENT_DATE] = '';

                    return new PostLtCsvPaymentRowModel('line-1', $row, '');
                },
                'expectedViolations' => [
                    'line-1.paymentDate [1 column] This value should not be blank. Value: "".',
                ],
            ],
            '5.' => [
                'assertMessage' => 'Payment date must be in correct format (yyyy.mm.dd).',
                'dataUpdater' => static function (array $row): PostLtCsvPaymentRowModel {
                    $row[PostLtCsvPaymentRowModel::INPUT_KEY_PAYMENT_DATE] = '2011-12-13';

                    return new PostLtCsvPaymentRowModel('line-1', $row, '');
                },
                'expectedViolations' => [
                    'line-1.paymentDate [1 column] This value is not valid. Valid formats: "yyyy.mm.dd". Value: "2011-12-13".',
                ],
            ],
            '6.' => [
                'assertMessage' => 'Payment date cannot have trailing characters.',
                'dataUpdater' => static function (array $row): PostLtCsvPaymentRowModel {
                    $row[PostLtCsvPaymentRowModel::INPUT_KEY_PAYMENT_DATE] = '-2011.12.13 00:00';

                    return new PostLtCsvPaymentRowModel('line-1', $row, '');
                },
                'expectedViolations' => [
                    'line-1.paymentDate [1 column] This value is not valid. Valid formats: "yyyy.mm.dd". Value: "-2011.12.13 00:00".',
                ],
            ],
            '7.' => [
                'assertMessage' => 'Bank account number cannot be blank.',
                'dataUpdater' => static function (array $row): PostLtCsvPaymentRowModel {
                    $row[PostLtCsvPaymentRowModel::INPUT_KEY_BANK_ACCOUNT_NUMBER] = '';

                    return new PostLtCsvPaymentRowModel('line-1', $row, '');
                },
                'expectedViolations' => [
                    'line-1.bankAccountNumber [2 column] This value should not be blank. Value: "".',
                ],
            ],
            '8.' => [
                'assertMessage' => 'Payment details cannot be blank.',
                'dataUpdater' => static function (array $row): PostLtCsvPaymentRowModel {
                    $row[PostLtCsvPaymentRowModel::INPUT_KEY_PAYMENT_DETAILS] = '';

                    return new PostLtCsvPaymentRowModel('line-1', $row, '');
                },
                'expectedViolations' => [
                    'line-1.paymentDetails [3 column] This value should not be blank. Value: "".',
                ],
            ],
            '9.' => [
                'assertMessage' => 'Payed by name cannot be blank.',
                'dataUpdater' => static function (array $row): PostLtCsvPaymentRowModel {
                    $row[PostLtCsvPaymentRowModel::INPUT_KEY_PAYED_BY_NAME] = '';

                    return new PostLtCsvPaymentRowModel('line-1', $row, '');
                },
                'expectedViolations' => [
                    'line-1.payedByName [5 column] This value should not be blank. Value: "".',
                ],
            ],
            '10.' => [
                'assertMessage' => 'Amount cannot be blank and must be number greater than zero.',
                'dataUpdater' => static function (array $row): PostLtCsvPaymentRowModel {
                    $row[PostLtCsvPaymentRowModel::INPUT_KEY_AMOUNT] = '';

                    return new PostLtCsvPaymentRowModel('line-1', $row, '');
                },
                'expectedViolations' => [
                    'line-1.amount [8 column] This value should not be blank. Value: "".',
                ],
            ],
            '11.' => [
                'assertMessage' => 'Amount must be formatted as float.',
                'dataUpdater' => static function (array $row): PostLtCsvPaymentRowModel {
                    $row[PostLtCsvPaymentRowModel::INPUT_KEY_AMOUNT] = '1';

                    return new PostLtCsvPaymentRowModel('line-1', $row, '');
                },
                'expectedViolations' => [
                    'line-1.amount [8 column] Value must be formatted as float "x.yy". Value: "1".',
                ],
            ],
            '12.' => [
                'assertMessage' => 'Payment code must be integer.',
                'dataUpdater' => static function (array $row): PostLtCsvPaymentRowModel {
                    $row[PostLtCsvPaymentRowModel::INPUT_KEY_PAYMENT_CODE] = '99abc';

                    return new PostLtCsvPaymentRowModel('line-1', $row, '');
                },
                'expectedViolations' => ['line-1.paymentCode [7 column] This value should be of type digit. Value: "99abc".'],
            ],
            '13.' => [
                'assertMessage' => 'Payment code can be blank.',
                'dataUpdater' => static function (array $row): PostLtCsvPaymentRowModel {
                    $row[PostLtCsvPaymentRowModel::INPUT_KEY_PAYMENT_CODE] = '';

                    return new PostLtCsvPaymentRowModel('line-1', $row, '');
                },
                'expectedViolations' => [],
            ],
            '14.' => [
                'assertMessage' => 'Amount must be positive.',
                'dataUpdater' => static function (array $row): PostLtCsvPaymentRowModel {
                    $row[PostLtCsvPaymentRowModel::INPUT_KEY_AMOUNT] = '-1.00';

                    return new PostLtCsvPaymentRowModel('line-1', $row, '');
                },
                'expectedViolations' => [
                    'line-1.amount [8 column] Value must be formatted as float "x.yy". Value: "-1.00".',
                ],
            ],
            '15.' => [
                'assertMessage' => 'Currency cannot be blank and must be from supported currency list.',
                'dataUpdater' => static function (array $row): PostLtCsvPaymentRowModel {
                    $row[PostLtCsvPaymentRowModel::INPUT_KEY_CURRENCY] = '';

                    return new PostLtCsvPaymentRowModel('line-1', $row, '');
                },
                'expectedViolations' => [
                    'line-1.currency [9 column] This value should not be blank. Value: "".',
                    'line-1.currency [9 column] The value you selected is not a valid choice. Valid choices: "EUR". Value: "".',
                ],
            ],
            '16.' => [
                'assertMessage' => 'Bank transfer code cannot be blank.',
                'dataUpdater' => static function (array $row): PostLtCsvPaymentRowModel {
                    $row[PostLtCsvPaymentRowModel::INPUT_KEY_BANK_TRANSFER_CODE] = '';

                    return new PostLtCsvPaymentRowModel('line-1', $row, '');
                },
                'expectedViolations' => [
                    'line-1.bankTransferCode [10 column] This value should not be blank. Value: "".',
                    'line-1.bankTransferCode [10 column] This value should be of type digit. Value: "".',
                ],
            ],
            '17.' => [
                'assertMessage' => 'Bank transfer code must be integer.',
                'dataUpdater' => static function (array $row): PostLtCsvPaymentRowModel {
                    $row[PostLtCsvPaymentRowModel::INPUT_KEY_BANK_TRANSFER_CODE] = '99abc';

                    return new PostLtCsvPaymentRowModel('line-1', $row, '');
                },
                'expectedViolations' => ['line-1.bankTransferCode [10 column] This value should be of type digit. Value: "99abc".'],
            ],
            '18.' => [
                'assertMessage' => 'Bank transfer code must be positive.',
                'dataUpdater' => static function (array $row): PostLtCsvPaymentRowModel {
                    $row[PostLtCsvPaymentRowModel::INPUT_KEY_BANK_TRANSFER_CODE] = '-1';

                    return new PostLtCsvPaymentRowModel('line-1', $row, '');
                },
                'expectedViolations' => ['line-1.bankTransferCode [10 column] This value should be of type digit. Value: "-1".'],
            ],
            '19.' => [
                'assertMessage' => 'Bank transfer date cannot be blank.',
                'dataUpdater' => static function (array $row): PostLtCsvPaymentRowModel {
                    $row[PostLtCsvPaymentRowModel::INPUT_KEY_BANK_TRANSFER_DATE] = '';

                    return new PostLtCsvPaymentRowModel('line-1', $row, '');
                },
                'expectedViolations' => [
                    'line-1.bankTransferDate [11 column] This value should not be blank. Value: "".',
                ],
            ],
            '20.' => [
                'assertMessage' => 'Bank transfer date must be in correct format (yyyy.mm.dd).',
                'dataUpdater' => static function (array $row): PostLtCsvPaymentRowModel {
                    $row[PostLtCsvPaymentRowModel::INPUT_KEY_BANK_TRANSFER_DATE] = '2011-12-13';

                    return new PostLtCsvPaymentRowModel('line-1', $row, '');
                },
                'expectedViolations' => [
                    'line-1.bankTransferDate [11 column] This value is not valid. Valid formats: "yyyy.mm.dd". Value: "2011-12-13".',
                ],
            ],
            '21.' => [
                'assertMessage' => 'Bank transfer date cannot have trailing characters.',
                'dataUpdater' => static function (array $row): PostLtCsvPaymentRowModel {
                    $row[PostLtCsvPaymentRowModel::INPUT_KEY_BANK_TRANSFER_DATE] = '-2011-12-13 00:00';

                    return new PostLtCsvPaymentRowModel('line-1', $row, '');
                },
                'expectedViolations' => [
                    'line-1.bankTransferDate [11 column] This value is not valid. Valid formats: "yyyy.mm.dd". Value: "-2011-12-13 00:00".',
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
        $row = [PostLtCsvPaymentRowModel::INPUT_KEY_AMOUNT => $amount];
        /* do */
        $postLtCsvPaymentRowModel = new PostLtCsvPaymentRowModel($lineId = 'line-1', $row, $sourceString = 'source string');
        /* assert */
        self::assertSame($expectedAmountInCents, $postLtCsvPaymentRowModel->getAmountInCents());
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

    /**
     * @covers ::getPaymentDateObject
     */
    public function testGetPaymentDateObject(): void
    {
        /* setup */
        $row = [PostLtCsvPaymentRowModel::INPUT_KEY_PAYMENT_DATE => '2011.12.13'];
        /* do */
        $postLtCsvPaymentRowModel = new PostLtCsvPaymentRowModel($lineId = 'line-1', $row, $sourceString = 'source string');
        /* assert */
        self::assertEquals(new DateTimeImmutable('2011-12-13 00:00:00'), $postLtCsvPaymentRowModel->getPaymentDateObject());
    }

    /**
     * @covers ::getBankTransferDateObject
     */
    public function testGetBankTransferDateObject(): void
    {
        /* setup */
        $row = [PostLtCsvPaymentRowModel::INPUT_KEY_BANK_TRANSFER_DATE => '2011.12.13'];
        /* do */
        $postLtCsvPaymentRowModel = new PostLtCsvPaymentRowModel($lineId = 'line-1', $row, $sourceString = 'source string');
        /* assert */
        self::assertEquals(new DateTimeImmutable('2011-12-13 00:00:00'), $postLtCsvPaymentRowModel->getBankTransferDateObject());
    }
}
