<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Model\SwedbankCsv;

use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Mapping\ClassMetadata;

use function implode;
use function sprintf;

/**
 * Example row:
 * LT357300010133333333,20,2017-09-04,Vardenis Pavardenis | 36508080921 | 36508080921 | AGBLLT2XXXX | LT664010051003333333,EMA-0001790/ / / / / / / /
 * ,9.00,EUR,K,2017090401228987,MK,,
 */
class AbstractSwedbankCsvPaymentRowModel
{
    /**
     * E.g. LT357300010133333333
     */
    public const INPUT_KEY_ACCOUNT_NUMBER = 0;
    /**
     * 10 - Opening balance
     * 20 - Transaction
     * 82 - Turnover
     * 86 - Closing balance
     * 900 - Accrued interest
     *
     * @see AbstractSwedbankCsvPaymentRowModel::RECORD_TYPE_OPENING_BALANCE
     * @see AbstractSwedbankCsvPaymentRowModel::RECORD_TYPE_TRANSACTION
     * @see AbstractSwedbankCsvPaymentRowModel::RECORD_TYPE_TURNOVER
     * @see AbstractSwedbankCsvPaymentRowModel::RECORD_TYPE_CLOSING_BALANCE
     * @see AbstractSwedbankCsvPaymentRowModel::RECORD_TYPE_ACCRUED_INTEREST
     */
    public const INPUT_KEY_RECORD_TYPE = 1;
    /**
     * E.g. 2017-09-04
     */
    public const INPUT_KEY_TRANSACTION_DATE = 2;
    /**
     * Name, ID code, bank code, account number. Separator "|".
     * E.g. Vardenis Pavardenis | 36508080921 | 36508080921 | AGBLLT2XXXX | LT664010051003333333
     * E.g. Vardenis Pavardenis | AGBLLT2XXXX | LT664010051003333333
     */
    public const INPUT_KEY_PARTY = 3;
    public const INPUT_KEY_DETAILS = 4;
    /**
     * Decimals separated by "." (dot)
     * E.g. 9.00
     */
    public const INPUT_KEY_AMOUNT = 5;
    /**
     * E.g. EUR
     *
     * * @see AbstractSwedbankCsvPaymentRowModel::CURRENCY_EUR
     */
    public const INPUT_KEY_CURRENCY = 6;
    /**
     * "K" - Credit transaction
     * "D" - Debit transaction
     *
     * @see AbstractSwedbankCsvPaymentRowModel::INDICATOR_CREDIT
     * @see AbstractSwedbankCsvPaymentRowModel::INDICATOR_DEBIT
     */
    public const INPUT_KEY_DEBIT_CREDIT_INDICATOR = 7;
    /**
     * E.g. 2017090401228987
     */
    public const INPUT_KEY_TRANSACTION_REFERENCE = 8;
    /**
     * No exact documentation, seems always to be "MK" for transactions, other seen values: MV, S, K2, LS, AS
     */
    public const INPUT_KEY_TRANSACTION_TYPE = 9;
    /**
     * Always empty, doc says "Not used".
     */
    public const INPUT_KEY_CLIENT_REFERENCE = 10;
    /**
     * Customer specific reference.
     */
    public const INPUT_KEY_DOCUMENT_NUMBER = 11;

    public const COLUMN_COUNT = 12;

    public const RECORD_TYPE_OPENING_BALANCE = '10';
    public const RECORD_TYPE_TRANSACTION = '20';
    public const RECORD_TYPE_TURNOVER = '82';
    public const RECORD_TYPE_CLOSING_BALANCE = '86';
    public const RECORD_TYPE_ACCRUED_INTEREST = '900';

    public const CURRENCY_EUR = 'EUR';

    public const INDICATOR_CREDIT = 'K';
    public const INDICATOR_DEBIT = 'D';

    public const TRANSACTION_TYPE_MK = 'MK';

    /** @var string */
    protected $lineId;
    /** @var string[] */
    protected $sourceRow;
    /** @var string */
    private $sourceString;
    /** @var string */
    protected $accountNumber;
    /** @var string */
    protected $recordType;
    /** @var string */
    protected $transactionDate;
    /** @var string */
    protected $party;
    /** @var string */
    protected $details;
    /** @var string */
    protected $amount;
    /** @var string */
    protected $currency;
    /** @var string */
    protected $debitCreditIndicator;
    /** @var string */
    protected $transactionReference;
    /** @var string */
    protected $transactionType;
    /** @var string */
    protected $clientReference;
    /** @var string */
    protected $documentNumber;

    /**
     * @param string[] $sourceRow
     */
    public function __construct(string $lineId, array $sourceRow, string $sourceString)
    {
        $this->lineId = $lineId;
        $this->sourceRow = $sourceRow;
        $this->sourceString = $sourceString;
        $this->accountNumber = $sourceRow[self::INPUT_KEY_ACCOUNT_NUMBER] ?? '';
        $this->recordType = $sourceRow[self::INPUT_KEY_RECORD_TYPE] ?? '';
        $this->transactionDate = $sourceRow[self::INPUT_KEY_TRANSACTION_DATE] ?? '';
        $this->party = $sourceRow[self::INPUT_KEY_PARTY] ?? '';
        $this->details = $sourceRow[self::INPUT_KEY_DETAILS] ?? '';
        $this->amount = $sourceRow[self::INPUT_KEY_AMOUNT] ?? '';
        $this->currency = $sourceRow[self::INPUT_KEY_CURRENCY] ?? '';
        $this->debitCreditIndicator = $sourceRow[self::INPUT_KEY_DEBIT_CREDIT_INDICATOR] ?? '';
        $this->transactionReference = $sourceRow[self::INPUT_KEY_TRANSACTION_REFERENCE] ?? '';
        $this->transactionType = $sourceRow[self::INPUT_KEY_TRANSACTION_TYPE] ?? '';
        $this->clientReference = $sourceRow[self::INPUT_KEY_CLIENT_REFERENCE] ?? '';
        $this->documentNumber = $sourceRow[self::INPUT_KEY_DOCUMENT_NUMBER] ?? '';
    }

    public function getLineId(): string
    {
        return $this->lineId;
    }

    /**
     * @return string[]
     */
    public function getSourceRow(): array
    {
        return $this->sourceRow;
    }

    public function getSourceString(): string
    {
        return $this->sourceString;
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    public function getRecordType(): string
    {
        return $this->recordType;
    }

    public function getTransactionDate(): string
    {
        return $this->transactionDate;
    }

    public function getParty(): string
    {
        return $this->party;
    }

    public function getDetails(): string
    {
        return $this->details;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getDebitCreditIndicator(): string
    {
        return $this->debitCreditIndicator;
    }

    public function getTransactionReference(): string
    {
        return $this->transactionReference;
    }

    public function getTransactionType(): string
    {
        return $this->transactionType;
    }

    public function getClientReference(): string
    {
        return $this->clientReference;
    }

    public function getDocumentNumber(): string
    {
        return $this->documentNumber;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        /**
         * @see AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_ACCOUNT_NUMBER
         */
        $metadata->addPropertyConstraints(
            'accountNumber',
            [new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::INPUT_KEY_ACCOUNT_NUMBER)])]
        );
        /**
         * @see AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_RECORD_TYPE
         */
        $availableRecordTypes = [
            self::RECORD_TYPE_OPENING_BALANCE,
            self::RECORD_TYPE_TRANSACTION,
            self::RECORD_TYPE_TURNOVER,
            self::RECORD_TYPE_CLOSING_BALANCE,
            self::RECORD_TYPE_ACCRUED_INTEREST,
        ];
        $metadata->addPropertyConstraints(
            'recordType',
            [
                new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::INPUT_KEY_RECORD_TYPE)]),
                new Choice(
                    [
                        'message' => sprintf(
                            '[%d column] The value you selected is not a valid choice. Valid choices: "%s".',
                            self::INPUT_KEY_RECORD_TYPE,
                            implode('", "', $availableRecordTypes)
                        ),
                        'choices' => $availableRecordTypes,
                    ]
                ),
            ]
        );
        /**
         * @see AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_TRANSACTION_DATE
         */
        $metadata->addPropertyConstraints(
            'transactionDate',
            [
                new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::INPUT_KEY_TRANSACTION_DATE)]),
                new Regex(
                    [
                        'message' => sprintf('[%d column] This value is not valid. Valid formats: "yyyy-mm-dd".', self::INPUT_KEY_TRANSACTION_DATE),
                        'pattern' => '/\d{4}-\d{2}-\d{2}/',
                    ]
                ),
            ]
        );
        /**
         * @see AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_PARTY
         */
        $metadata->addPropertyConstraints(
            'party',
            [new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::INPUT_KEY_PARTY)])]
        );
        /**
         * @see AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_DETAILS
         */
        $metadata->addPropertyConstraints(
            'details',
            [new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::INPUT_KEY_DETAILS)])]
        );
        /**
         * @see AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_AMOUNT
         */
        $metadata->addPropertyConstraints(
            'amount',
            [
                new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::INPUT_KEY_AMOUNT)]),
                new GreaterThan(
                    [
                        'message' => sprintf('[%d column] This value should be greater than 0.', self::INPUT_KEY_AMOUNT),
                        'value' => 0,
                    ]
                ),
                new Regex(
                    [
                        'message' => sprintf('[%d column] Value must be formatted as float "x.yy".', self::INPUT_KEY_AMOUNT),
                        'pattern' => '/\d+\.\d{2}/',
                    ]
                ),
            ]
        );
        /**
         * @see AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_CURRENCY
         */
        $availableCurrencies = [self::CURRENCY_EUR];
        $metadata->addPropertyConstraints(
            'currency',
            [
                new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::INPUT_KEY_CURRENCY)]),
                new Choice(
                    [
                        'message' => sprintf(
                            '[%d column] The value you selected is not a valid choice. Valid choices: "%s".',
                            self::INPUT_KEY_CURRENCY,
                            implode('", "', $availableCurrencies)
                        ),
                        'choices' => $availableCurrencies,
                    ]
                ),
            ]
        );
        /**
         * @see AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_DEBIT_CREDIT_INDICATOR
         */
        $availableDebitCreditIndicators = [
            self::INDICATOR_CREDIT,
            self::INDICATOR_DEBIT,
        ];
        $metadata->addPropertyConstraints(
            'debitCreditIndicator',
            [
                new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::INPUT_KEY_DEBIT_CREDIT_INDICATOR)]),
                new Choice(
                    [
                        'message' => sprintf(
                            '[%d column] The value you selected is not a valid choice. Valid choices: "%s".',
                            self::INPUT_KEY_DEBIT_CREDIT_INDICATOR,
                            implode('", "', $availableDebitCreditIndicators)
                        ),
                        'choices' => $availableDebitCreditIndicators,
                    ]
                ),
            ]
        );
        /**
         * @see AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_TRANSACTION_REFERENCE
         */
        $metadata->addPropertyConstraints(
            'transactionReference',
            [
                new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::INPUT_KEY_TRANSACTION_REFERENCE)]),
                new Regex(
                    [
                        'message' => sprintf('[%d column] Value must be formatted as 16 digits.', self::INPUT_KEY_TRANSACTION_REFERENCE),
                        'pattern' => '/\d{16}/',
                    ]
                ),
            ]
        );
        /**
         * @see AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_TRANSACTION_TYPE
         */
        $availableTransactionTypes = [
            self::TRANSACTION_TYPE_MK,
        ];
        $metadata->addPropertyConstraints(
            'transactionType',
            [
                new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::INPUT_KEY_TRANSACTION_TYPE)]),
                new Choice(
                    [
                        'message' => sprintf(
                            '[%d column] The value you selected is not a valid choice. Valid choices: "%s".',
                            self::INPUT_KEY_TRANSACTION_TYPE,
                            implode('", "', $availableTransactionTypes)
                        ),
                        'choices' => $availableTransactionTypes,
                    ]
                ),
            ]
        );
        /**
         * @see AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_CLIENT_REFERENCE
         */
        $metadata->addPropertyConstraints(
            'clientReference',
            [new Blank(['message' => sprintf('[%d column] This value should be blank, documentation says "Not used".', self::INPUT_KEY_CLIENT_REFERENCE)])]
        );
        /**
         * @see AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_DOCUMENT_NUMBER
         */
        $metadata->addPropertyConstraints('documentNumber', []);
    }
}
