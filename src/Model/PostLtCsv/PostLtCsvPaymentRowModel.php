<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Model\PostLtCsv;

use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Mapping\ClassMetadata;

use function implode;
use function sprintf;

/**
 * Example row:
 * 54001    2017.09.07    LT357300010133333333    XXXXXXX        Vardenė1 Pavardenis1    Vyturių g. XB, Raudondvaris, Kauno r. sa    11    148.50    EUR
 * 2566074    2017.09.08
 */
class PostLtCsvPaymentRowModel
{
    public const CSV_COLUMN_COUNT = 24;

    /**
     * Line identificator, e.g. "line-1".
     *
     * @var string
     */
    private $lineId;
    /**
     * Source row array from csv line.
     */
    private const PROPERTY_SOURCE_ROW = 'sourceRow';
    /** @var string[] */
    private $sourceRow;
    /**
     * Original csv string.
     *
     * @var string
     */
    private $sourceString;
    /**
     * Post code of post.lt post where payment was made. Required.
     * E.g. 54001
     */
    public const CSV_COLUMN_KEY_POST_CODE = 0;
    private const PROPERTY_POST_CODE = 'postCode';
    /** @var string */
    private $postCode;
    /**
     * Payment date. Required.
     * Date in format yyyy.mm.dd.
     * E.g. 2017.09.07
     */
    public const CSV_COLUMN_KEY_PAYMENT_DATE = 1;
    private const PROPERTY_PAYMENT_DATE = 'paymentDate';
    /** @var string */
    private $paymentDate;
    /**
     * (Your company) bank account where money where transferred. Required.
     * E.g. LT357300010133333333
     */
    public const CSV_COLUMN_KEY_BANK_ACCOUNT_NUMBER = 2;
    private const PROPERTY_BANK_ACCOUNT_NUMBER = 'bankAccountNumber';
    /** @var string */
    private $bankAccountNumber;
    /**
     * Payment details, whatever payer provided, e.g. order number. Required.
     */
    public const CSV_COLUMN_KEY_PAYMENT_DETAILS = 3;
    private const PROPERTY_PAYMENT_DETAILS = 'paymentDetails';
    /** @var string */
    private $paymentDetails;
    /**
     * Additional information maybe provided. Optional.
     */
    public const CSV_COLUMN_KEY_ADDITIONAL_INFORMATION = 4;
    private const PROPERTY_ADDITIONAL_INFORMATION = 'additionalInformation';
    /** @var string */
    private $additionalInformation;
    /**
     * The name of the payer. Required.
     */
    public const CSV_COLUMN_KEY_PAYED_BY_NAME = 5;
    private const PROPERTY_PAYED_BY_NAME = 'payedByName';
    /** @var string */
    private $payedByName;
    /**
     * The address of the payer. Optional.
     */
    public const CSV_COLUMN_KEY_PAYED_BY_ADDRESS = 6;
    private const PROPERTY_PAYED_BY_ADDRESS = 'payedByAddress';
    /** @var string */
    private $payedByAddress;
    /**
     * Payment code provided by payer. Optional.
     * E.g. 2566074
     */
    public const CSV_COLUMN_KEY_PAYMENT_CODE = 7;
    private const PROPERTY_PAYMENT_CODE = 'paymentCode';
    /** @var string */
    private $paymentCode;
    /**
     * Payed amount, decimals separated by "." (dot). Required.
     * E.g. 9.00
     */
    public const CSV_COLUMN_KEY_AMOUNT = 8;
    private const PROPERTY_AMOUNT = 'amount';
    /** @var string */
    private $amount;
    /**
     * Payment currency. Required.
     * E.g. EUR
     */
    public const CSV_COLUMN_KEY_CURRENCY = 9;
    private const PROPERTY_CURRENCY = 'currency';
    public const CURRENCY_EUR = 'EUR';
    /** @var string */
    private $currency;
    /**
     * Code of the transfer from postLt to (your company) bank account in format yyyy.mm.dd. Required.
     */
    public const CSV_COLUMN_KEY_BANK_TRANSFER_CODE = 10;
    private const PROPERTY_BANK_TRANSFER_CODE = 'bankTransferCode';
    /** @var string */
    private $bankTransferCode;
    /**
     * Date of the transfer from postLt to (your company) bank account in format yyyy.mm.dd. Required.
     */
    public const CSV_COLUMN_KEY_BANK_TRANSFER_DATE = 11;
    private const PROPERTY_BANK_TRANSFER_DATE = 'bankTransferDate';
    /** @var string */
    private $bankTransferDate;

    /**
     * Other fields at the end are optional, usually blank. Documentation says "Counter from/to number".
     */

    /**
     * @param string[] $sourceRow
     */
    public function __construct(string $lineId, array $sourceRow, string $sourceString)
    {
        $this->lineId = $lineId;
        $this->sourceRow = $sourceRow;
        $this->sourceString = $sourceString;
        $this->postCode = $sourceRow[self::CSV_COLUMN_KEY_POST_CODE] ?? '';
        $this->paymentDate = $sourceRow[self::CSV_COLUMN_KEY_PAYMENT_DATE] ?? '';
        $this->bankAccountNumber = $sourceRow[self::CSV_COLUMN_KEY_BANK_ACCOUNT_NUMBER] ?? '';
        $this->paymentDetails = $sourceRow[self::CSV_COLUMN_KEY_PAYMENT_DETAILS] ?? '';
        $this->additionalInformation = $sourceRow[self::CSV_COLUMN_KEY_ADDITIONAL_INFORMATION] ?? '';
        $this->payedByName = $sourceRow[self::CSV_COLUMN_KEY_PAYED_BY_NAME] ?? '';
        $this->payedByAddress = $sourceRow[self::CSV_COLUMN_KEY_PAYED_BY_ADDRESS] ?? '';
        $this->paymentCode = $sourceRow[self::CSV_COLUMN_KEY_PAYMENT_CODE] ?? '';
        $this->amount = $sourceRow[self::CSV_COLUMN_KEY_AMOUNT] ?? '';
        $this->currency = $sourceRow[self::CSV_COLUMN_KEY_CURRENCY] ?? '';
        $this->bankTransferCode = $sourceRow[self::CSV_COLUMN_KEY_BANK_TRANSFER_CODE] ?? '';
        $this->bankTransferDate = $sourceRow[self::CSV_COLUMN_KEY_BANK_TRANSFER_DATE] ?? '';
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

    public function getRawPostCode(): string
    {
        return $this->postCode;
    }

    public function getRawPaymentDate(): string
    {
        return $this->paymentDate;
    }

    public function getRawBankAccountNumber(): string
    {
        return $this->bankAccountNumber;
    }

    public function getRawPaymentDetails(): string
    {
        return $this->paymentDetails;
    }

    public function getRawAdditionalInformation(): string
    {
        return $this->additionalInformation;
    }

    public function getRawPayedByName(): string
    {
        return $this->payedByName;
    }

    public function getRawPayedByAddress(): string
    {
        return $this->payedByAddress;
    }

    public function getRawPaymentCode(): string
    {
        return $this->paymentCode;
    }

    public function getRawAmount(): string
    {
        return $this->amount;
    }

    public function getRawCurrency(): string
    {
        return $this->currency;
    }

    public function getRawBankTransferCode(): string
    {
        return $this->bankTransferCode;
    }

    public function getRawBankTransferDate(): string
    {
        return $this->bankTransferDate;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        /* column count */
        $metadata->addPropertyConstraints(
            self::PROPERTY_SOURCE_ROW,
            [new Count(['min' => self::CSV_COLUMN_COUNT, 'max' => self::CSV_COLUMN_COUNT])]
        );
        /**
         * @see PostLtCsvPaymentRowModel::CSV_COLUMN_KEY_POST_CODE
         */
        $metadata->addPropertyConstraints(
            self::PROPERTY_POST_CODE,
            [
                new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::CSV_COLUMN_KEY_POST_CODE)]),
                new Type(
                    [
                        'type' => 'digit',
                        'message' => sprintf('[%d column] This value should be of type digit.', self::CSV_COLUMN_KEY_POST_CODE),
                    ]
                ),
            ]
        );
        /**
         * @see PostLtCsvPaymentRowModel::CSV_COLUMN_KEY_PAYMENT_DATE
         */
        $metadata->addPropertyConstraints(
            self::PROPERTY_PAYMENT_DATE,
            [
                new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::CSV_COLUMN_KEY_PAYMENT_DATE)]),
                new Regex(
                    [
                        'message' => sprintf('[%d column] This value is not valid. Valid formats: "yyyy.mm.dd".', self::CSV_COLUMN_KEY_PAYMENT_DATE),
                        'pattern' => '/^\d{4}\.\d{2}\.\d{2}$/',
                    ]
                ),
            ]
        );
        /**
         * @see PostLtCsvPaymentRowModel::CSV_COLUMN_KEY_BANK_ACCOUNT_NUMBER
         */
        $metadata->addPropertyConstraints(
            self::PROPERTY_BANK_ACCOUNT_NUMBER,
            [new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::CSV_COLUMN_KEY_BANK_ACCOUNT_NUMBER)])]
        );
        /**
         * @see PostLtCsvPaymentRowModel::CSV_COLUMN_KEY_PAYMENT_DETAILS
         */
        $metadata->addPropertyConstraints(
            self::PROPERTY_PAYMENT_DETAILS,
            [new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::CSV_COLUMN_KEY_PAYMENT_DETAILS)])]
        );
        /**
         * @see PostLtCsvPaymentRowModel::CSV_COLUMN_KEY_ADDITIONAL_INFORMATION
         */
        $metadata->addPropertyConstraints(self::PROPERTY_ADDITIONAL_INFORMATION, []);
        /**
         * @see PostLtCsvPaymentRowModel::CSV_COLUMN_KEY_PAYED_BY_NAME
         */
        $metadata->addPropertyConstraints(
            self::PROPERTY_PAYED_BY_NAME,
            [new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::CSV_COLUMN_KEY_PAYED_BY_NAME)])]
        );
        /**
         * @see PostLtCsvPaymentRowModel::CSV_COLUMN_KEY_PAYED_BY_ADDRESS
         */
        $metadata->addPropertyConstraints(self::PROPERTY_PAYED_BY_ADDRESS, []);
        /**
         * @see PostLtCsvPaymentRowModel::CSV_COLUMN_KEY_PAYMENT_CODE
         */
        $metadata->addPropertyConstraints(
            self::PROPERTY_PAYMENT_CODE,
            [
                new Regex(
                    [
                        // blank or digit
                        'pattern' => '/^\d*$/',
                        'message' => sprintf('[%d column] This value should be of type digit.', self::CSV_COLUMN_KEY_PAYMENT_CODE),
                    ]
                ),
            ]
        );
        /**
         * @see PostLtCsvPaymentRowModel::CSV_COLUMN_KEY_AMOUNT
         */
        $metadata->addPropertyConstraints(
            self::PROPERTY_AMOUNT,
            [
                new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::CSV_COLUMN_KEY_AMOUNT)]),
                new Regex(
                    [
                        'message' => sprintf('[%d column] Value must be formatted as float "x.yy".', self::CSV_COLUMN_KEY_AMOUNT),
                        'pattern' => '/^\d+\.\d{2}$/',
                    ]
                ),
            ]
        );

        /**
         * @see PostLtCsvPaymentRowModel::CSV_COLUMN_KEY_CURRENCY
         */
        $availableCurrencies = [self::CURRENCY_EUR];
        $metadata->addPropertyConstraints(
            self::PROPERTY_CURRENCY,
            [
                new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::CSV_COLUMN_KEY_CURRENCY)]),
                new Choice(
                    [
                        'message' => sprintf(
                            '[%d column] The value you selected is not a valid choice. Valid choices: "%s".',
                            self::CSV_COLUMN_KEY_CURRENCY,
                            implode('", "', $availableCurrencies)
                        ),
                        'choices' => $availableCurrencies,
                    ]
                ),
            ]
        );
        /**
         * @see PostLtCsvPaymentRowModel::CSV_COLUMN_KEY_BANK_TRANSFER_CODE
         */
        $metadata->addPropertyConstraints(
            self::PROPERTY_BANK_TRANSFER_CODE,
            [
                new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::CSV_COLUMN_KEY_BANK_TRANSFER_CODE)]),
                new Type(
                    [
                        'type' => 'digit',
                        'message' => sprintf('[%d column] This value should be of type digit.', self::CSV_COLUMN_KEY_BANK_TRANSFER_CODE),
                    ]
                ),
            ]
        );
        /**
         * @see PostLtCsvPaymentRowModel::CSV_COLUMN_KEY_BANK_TRANSFER_DATE
         */
        $metadata->addPropertyConstraints(
            self::PROPERTY_BANK_TRANSFER_DATE,
            [
                new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::CSV_COLUMN_KEY_BANK_TRANSFER_DATE)]),
                new Regex(
                    [
                        'message' => sprintf('[%d column] This value is not valid. Valid formats: "yyyy.mm.dd".', self::CSV_COLUMN_KEY_BANK_TRANSFER_DATE),
                        'pattern' => '/^\d{4}\.\d{2}\.\d{2}$/',
                    ]
                ),
            ]
        );
    }
}
