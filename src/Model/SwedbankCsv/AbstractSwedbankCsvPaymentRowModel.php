<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Model\SwedbankCsv;

use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Count;
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
    public const COLUMN_COUNT = 12;

    /**
     * Line identificator, e.g. "line-1".
     *
     * @var string
     */
    protected $sourceLineId;
    /**
     * Source row array from csv line.
     */
    protected const PROPERTY_SOURCE_ROW = 'sourceRow';
    /** @var string[] */
    protected $sourceRow;
    /**
     * Original csv string.
     *
     * @var string
     */
    protected $sourceString;
    /**
     * (Your company) bank account where money where transferred.
     * E.g. LT357300010133333333
     */
    public const INPUT_KEY_BANK_ACCOUNT_NUMBER = 0;
    protected const PROPERTY_BANK_ACCOUNT_NUMBER = 'bankAccountNumber';
    /** @var string */
    protected $bankAccountNumber;
    /**
     * Record type.
     *
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
    protected const PROPERTY_RECORD_TYPE = 'recordType';
    public const RECORD_TYPE_OPENING_BALANCE = '10';
    public const RECORD_TYPE_TRANSACTION = '20';
    public const RECORD_TYPE_TURNOVER = '82';
    public const RECORD_TYPE_CLOSING_BALANCE = '86';
    public const RECORD_TYPE_ACCRUED_INTEREST = '900';
    /** @var string */
    protected $recordType;
    /**
     * Transaction date.
     * E.g. 2017-09-04
     */
    public const INPUT_KEY_TRANSACTION_DATE = 2;
    protected const PROPERTY_TRANSACTION_DATE = 'transactionDate';
    /** @var string */
    protected $transactionDate;
    /**
     * Party information. Name, ID code, bank code, account number. Separator "|".
     * E.g. Vardenis Pavardenis | 36508080921 | 36508080921 | AGBLLT2XXXX | LT664010051003333333
     * E.g. Vardenis Pavardenis | AGBLLT2XXXX | LT664010051003333333
     */
    public const INPUT_KEY_PARTY = 3;
    protected const PROPERTY_PARTY = 'party';
    /** @var string */
    protected $party;
    /**
     * Operation details. For "transaction" record type field contains multiple values concatenated with "/" symbol.
     * E.g. XXX 333333/ / / / / / / /
     */
    public const INPUT_KEY_DETAILS = 4;
    protected const PROPERTY_DETAILS = 'details';
    /** @var string */
    protected $details;
    /**
     * Payed amount, decimals separated by "." (dot).
     * E.g. 9.00
     */
    public const INPUT_KEY_AMOUNT = 5;
    protected const PROPERTY_AMOUNT = 'amount';
    /** @var string */
    protected $amount;
    /**
     * Payment currency.
     * E.g. EUR
     *
     * * @see AbstractSwedbankCsvPaymentRowModel::CURRENCY_EUR
     */
    public const INPUT_KEY_CURRENCY = 6;
    public const CURRENCY_EUR = 'EUR';
    protected const PROPERTY_CURRENCY = 'currency';
    /** @var string */
    protected $currency;
    /**
     * Credit/debit indicator.
     *
     * "K" - Credit transaction
     * "D" - Debit transaction
     *
     * @see AbstractSwedbankCsvPaymentRowModel::INDICATOR_CREDIT
     * @see AbstractSwedbankCsvPaymentRowModel::INDICATOR_DEBIT
     */
    public const INPUT_KEY_DEBIT_CREDIT_INDICATOR = 7;
    protected const PROPERTY_DEBIT_CREDIT_INDICATOR = 'debitCreditIndicator';
    public const INDICATOR_CREDIT = 'K';
    public const INDICATOR_DEBIT = 'D';
    /** @var string */
    protected $debitCreditIndicator;
    /**
     * Transaction reference.
     * E.g. 2017090401228987
     */
    public const INPUT_KEY_TRANSACTION_REFERENCE = 8;
    protected const PROPERTY_TRANSACTION_REFERENCE = 'transactionReference';
    /** @var string */
    protected $transactionReference;
    /**
     * No exact documentation, seems always to be "MK" for transactions, other seen values: MV, S, K2, LS, AS
     */
    public const INPUT_KEY_TRANSACTION_TYPE = 9;
    protected const PROPERTY_TRANSACTION_TYPE = 'transactionType';
    public const TRANSACTION_TYPE_MK = 'MK';
    /** @var string */
    protected $transactionType;
    /**
     * Always empty, doc says "Not used".
     */
    public const INPUT_KEY_CLIENT_REFERENCE = 10;
    protected const PROPERTY_CLIENT_REFERENCE = 'clientReference';
    /** @var string */
    protected $clientReference;
    /**
     * Customer specific reference.
     */
    public const INPUT_KEY_DOCUMENT_NUMBER = 11;
    protected const PROPERTY_DOCUMENT_NUMBER = 'documentNumber';
    /** @var string */
    protected $documentNumber;

    /**
     * @param string[] $sourceRow
     */
    public function __construct(string $lineId, array $sourceRow, string $sourceString)
    {
        $this->sourceLineId = $lineId;
        $this->sourceRow = $sourceRow;
        $this->sourceString = $sourceString;
        $this->bankAccountNumber = $sourceRow[self::INPUT_KEY_BANK_ACCOUNT_NUMBER] ?? '';
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

    public function getSourceLineId(): string
    {
        return $this->sourceLineId;
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

    public function getBankAccountNumber(): string
    {
        return $this->bankAccountNumber;
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
        /* column count */
        $metadata->addPropertyConstraints(
            self::PROPERTY_SOURCE_ROW,
            [new Count(['min' => self::COLUMN_COUNT, 'max' => self::COLUMN_COUNT])]
        );
        /**
         * @see AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_BANK_ACCOUNT_NUMBER
         */
        $metadata->addPropertyConstraints(
            self::PROPERTY_BANK_ACCOUNT_NUMBER,
            [new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::INPUT_KEY_BANK_ACCOUNT_NUMBER)])]
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
            self::PROPERTY_RECORD_TYPE,
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
            self::PROPERTY_TRANSACTION_DATE,
            [
                new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::INPUT_KEY_TRANSACTION_DATE)]),
                new Regex(
                    [
                        'message' => sprintf('[%d column] This value is not valid. Valid formats: "yyyy-mm-dd".', self::INPUT_KEY_TRANSACTION_DATE),
                        'pattern' => '/^\d{4}-\d{2}-\d{2}$/',
                    ]
                ),
            ]
        );
        /**
         * @see AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_PARTY
         */
        $metadata->addPropertyConstraints(
            self::PROPERTY_PARTY,
            [new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::INPUT_KEY_PARTY)])]
        );
        /**
         * @see AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_DETAILS
         */
        $metadata->addPropertyConstraints(
            self::PROPERTY_DETAILS,
            [new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::INPUT_KEY_DETAILS)])]
        );
        /**
         * @see AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_AMOUNT
         */
        $metadata->addPropertyConstraints(
            self::PROPERTY_AMOUNT,
            [
                new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::INPUT_KEY_AMOUNT)]),
                new Regex(
                    [
                        'message' => sprintf('[%d column] Value must be formatted as float "x.yy".', self::INPUT_KEY_AMOUNT),
                        'pattern' => '/^\d+\.\d{2}$/',
                    ]
                ),
            ]
        );
        /**
         * @see AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_CURRENCY
         */
        $availableCurrencies = [self::CURRENCY_EUR];
        $metadata->addPropertyConstraints(
            self::PROPERTY_CURRENCY,
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
            self::PROPERTY_DEBIT_CREDIT_INDICATOR,
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
            self::PROPERTY_TRANSACTION_REFERENCE,
            [
                new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::INPUT_KEY_TRANSACTION_REFERENCE)]),
                new Regex(
                    [
                        'message' => sprintf('[%d column] Value must be formatted as 16 digits.', self::INPUT_KEY_TRANSACTION_REFERENCE),
                        'pattern' => '/^\d{16}$/',
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
            self::PROPERTY_TRANSACTION_TYPE,
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
            self::PROPERTY_CLIENT_REFERENCE,
            [new Blank(['message' => sprintf('[%d column] This value should be blank, documentation says "Not used".', self::INPUT_KEY_CLIENT_REFERENCE)])]
        );
        /**
         * @see AbstractSwedbankCsvPaymentRowModel::INPUT_KEY_DOCUMENT_NUMBER
         */
        $metadata->addPropertyConstraints(self::PROPERTY_DOCUMENT_NUMBER, []);
    }
}
