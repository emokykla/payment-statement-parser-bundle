<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Model\PostLtCsv;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Mapping\ClassMetadata;

use function implode;
use function sprintf;
use function str_replace;

/**
 * Example row:
 * 54001    2017.09.07    LT357300010133333333    XXXXXXX        Vardenė1 Pavardenis1    Vyturių g. XB, Raudondvaris, Kauno r. sa    11    148.50    EUR
 * 2566074    2017.09.08
 */
class PostLtCsvPaymentRowModel
{
    /**
     * E.g. 54001
     */
    public const INPUT_KEY_POST_CODE = 0;
    /**
     * Date in format yyyy.mm.dd.
     * E.g. 2017.09.07
     */
    public const INPUT_KEY_PAYMENT_DATE = 1;
    /**
     * E.g. LT357300010133333333
     */
    public const INPUT_KEY_BANK_ACCOUNT_NUMBER = 2;
    /**
     * Payment details that payer provider, e.g. order number.
     */
    public const INPUT_KEY_PAYMENT_DETAILS = 3;
    public const INPUT_KEY_ADDITIONAL_INFORMATION = 4;
    public const INPUT_KEY_PAYED_BY_NAME = 5;
    public const INPUT_KEY_PAYED_BY_ADDRESS = 6;
    /**
     * E.g. 2566074
     */
    public const INPUT_KEY_PAYMENT_CODE = 7;
    /**
     * Decimals separated by "." (dot)
     * E.g. 9.00
     */
    public const INPUT_KEY_AMOUNT = 8;
    /**
     * E.g. EUR
     *
     * * @see PostLtCsvPaymentRowModel::CURRENCY_EUR
     */
    public const INPUT_KEY_CURRENCY = 9;
    /**
     * Code of the transfer from postLt to our company bank account
     */
    public const INPUT_KEY_BANK_TRANSFER_CODE = 10;
    /**
     * Data of the transfer from postLt to our company bank account in format yyyy.mm.dd
     */
    public const INPUT_KEY_BANK_TRANSFER_DATE = 11;

    /**
     * Other fields at the end are optional, usually blank. Documentation says "Counter from/to number".
     */

    public const COLUMN_COUNT = 24;

    public const CURRENCY_EUR = 'EUR';

    /**
     * PROPERTY_* constants reflect field names.
     */
    private const PROPERTY_SOURCE_ROW = 'sourceRow';
    private const PROPERTY_POST_CODE = 'postCode';
    private const PROPERTY_PAYMENT_DATE = 'paymentDate';
    private const PROPERTY_BANK_ACCOUNT_NUMBER = 'bankAccountNumber';
    private const PROPERTY_PAYMENT_DETAILS = 'paymentDetails';
    private const PROPERTY_ADDITIONAL_INFORMATION = 'additionalInformation';
    private const PROPERTY_PAYED_BY_NAME = 'payedByName';
    private const PROPERTY_PAYED_BY_ADDRESS = 'payedByAddress';
    private const PROPERTY_PAYMENT_CODE = 'paymentCode';
    private const PROPERTY_AMOUNT = 'amount';
    private const PROPERTY_CURRENCY = 'currency';
    private const PROPERTY_BANK_TRANSFER_CODE = 'bankTransferCode';
    private const PROPERTY_BANK_TRANSFER_DATE = 'bankTransferDate';

    /** @var string */
    private $lineId;
    /** @var string[] */
    private $sourceRow;
    /** @var string */
    private $sourceString;
    /** @var string */
    private $postCode;
    /** @var string */
    private $paymentDate;
    /** @var string */
    private $bankAccountNumber;
    /** @var string */
    private $paymentDetails;
    /** @var string */
    private $additionalInformation;
    /** @var string */
    private $payedByName;
    /** @var string */
    private $payedByAddress;
    /** @var string */
    private $paymentCode;
    /** @var string */
    private $amount;
    /** @var string */
    private $currency;
    /** @var string */
    private $bankTransferCode;
    /** @var string */
    private $bankTransferDate;

    /**
     * @param string[] $sourceRow
     */
    public function __construct(string $lineId, array $sourceRow, string $sourceString)
    {
        $this->lineId = $lineId;
        $this->sourceRow = $sourceRow;
        $this->sourceString = $sourceString;
        $this->postCode = $sourceRow[self::INPUT_KEY_POST_CODE] ?? '';
        $this->paymentDate = $sourceRow[self::INPUT_KEY_PAYMENT_DATE] ?? '';
        $this->bankAccountNumber = $sourceRow[self::INPUT_KEY_BANK_ACCOUNT_NUMBER] ?? '';
        $this->paymentDetails = $sourceRow[self::INPUT_KEY_PAYMENT_DETAILS] ?? '';
        $this->additionalInformation = $sourceRow[self::INPUT_KEY_ADDITIONAL_INFORMATION] ?? '';
        $this->payedByName = $sourceRow[self::INPUT_KEY_PAYED_BY_NAME] ?? '';
        $this->payedByAddress = $sourceRow[self::INPUT_KEY_PAYED_BY_ADDRESS] ?? '';
        $this->paymentCode = $sourceRow[self::INPUT_KEY_PAYMENT_CODE] ?? '';
        $this->amount = $sourceRow[self::INPUT_KEY_AMOUNT] ?? '';
        $this->currency = $sourceRow[self::INPUT_KEY_CURRENCY] ?? '';
        $this->bankTransferCode = $sourceRow[self::INPUT_KEY_BANK_TRANSFER_CODE] ?? '';
        $this->bankTransferDate = $sourceRow[self::INPUT_KEY_BANK_TRANSFER_DATE] ?? '';
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

    public function getPostCode(): string
    {
        return $this->postCode;
    }

    public function getPaymentDate(): string
    {
        return $this->paymentDate;
    }

    public function getBankAccountNumber(): string
    {
        return $this->bankAccountNumber;
    }

    public function getPaymentDetails(): string
    {
        return $this->paymentDetails;
    }

    public function getAdditionalInformation(): string
    {
        return $this->additionalInformation;
    }

    public function getPayedByName(): string
    {
        return $this->payedByName;
    }

    public function getPayedByAddress(): string
    {
        return $this->payedByAddress;
    }

    public function getPaymentCode(): string
    {
        return $this->paymentCode;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getBankTransferCode(): string
    {
        return $this->bankTransferCode;
    }

    public function getBankTransferDate(): string
    {
        return $this->bankTransferDate;
    }

    public function getAmountInCents(): int
    {
        return (int) ((float) $this->getAmount() * 100);
    }

    public function getPaymentDateObject(): DateTimeImmutable
    {
        $dateString = $this->fixDateStringFormat($this->getPaymentDate());

        return new DateTimeImmutable($dateString);
    }

    public function getBankTransferDateObject(): DateTimeImmutable
    {
        $dateString = $this->fixDateStringFormat($this->getBankTransferDate());

        return new DateTimeImmutable($dateString);
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        /* column count */
        $metadata->addPropertyConstraints(
            self::PROPERTY_SOURCE_ROW,
            [new Count(['min' => self::COLUMN_COUNT, 'max' => self::COLUMN_COUNT])]
        );
        /**
         * @see PostLtCsvPaymentRowModel::INPUT_KEY_POST_CODE
         */
        $metadata->addPropertyConstraints(
            self::PROPERTY_POST_CODE,
            [
                new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::INPUT_KEY_POST_CODE)]),
                new Type(
                    [
                        'type' => 'digit',
                        'message' => sprintf('[%d column] This value should be of type digit.', self::INPUT_KEY_POST_CODE),
                    ]
                ),
            ]
        );
        /**
         * @see PostLtCsvPaymentRowModel::INPUT_KEY_PAYMENT_DATE
         */
        $metadata->addPropertyConstraints(
            self::PROPERTY_PAYMENT_DATE,
            [
                new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::INPUT_KEY_PAYMENT_DATE)]),
                new Regex(
                    [
                        'message' => sprintf('[%d column] This value is not valid. Valid formats: "yyyy.mm.dd".', self::INPUT_KEY_PAYMENT_DATE),
                        'pattern' => '/^\d{4}\.\d{2}\.\d{2}$/',
                    ]
                ),
            ]
        );
        /**
         * @see PostLtCsvPaymentRowModel::INPUT_KEY_BANK_ACCOUNT_NUMBER
         */
        $metadata->addPropertyConstraints(
            self::PROPERTY_BANK_ACCOUNT_NUMBER,
            [new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::INPUT_KEY_BANK_ACCOUNT_NUMBER)])]
        );
        /**
         * @see PostLtCsvPaymentRowModel::INPUT_KEY_PAYMENT_DETAILS
         */
        $metadata->addPropertyConstraints(
            self::PROPERTY_PAYMENT_DETAILS,
            [new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::INPUT_KEY_PAYMENT_DETAILS)])]
        );
        /**
         * @see PostLtCsvPaymentRowModel::INPUT_KEY_ADDITIONAL_INFORMATION
         */
        $metadata->addPropertyConstraints(self::PROPERTY_ADDITIONAL_INFORMATION, []);
        /**
         * @see PostLtCsvPaymentRowModel::INPUT_KEY_PAYED_BY_NAME
         */
        $metadata->addPropertyConstraints(
            self::PROPERTY_PAYED_BY_NAME,
            [new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::INPUT_KEY_PAYED_BY_NAME)])]
        );
        /**
         * @see PostLtCsvPaymentRowModel::INPUT_KEY_PAYED_BY_ADDRESS
         */
        $metadata->addPropertyConstraints(self::PROPERTY_PAYED_BY_ADDRESS, []);
        /**
         * @see PostLtCsvPaymentRowModel::INPUT_KEY_PAYMENT_CODE
         */
        $metadata->addPropertyConstraints(
            self::PROPERTY_PAYMENT_CODE,
            [
                new Regex(
                    [
                        // blank or digit
                        'pattern' => '/^\d*$/',
                        'message' => sprintf('[%d column] This value should be of type digit.', self::INPUT_KEY_PAYMENT_CODE),
                    ]
                ),
            ]
        );
        /**
         * @see PostLtCsvPaymentRowModel::INPUT_KEY_AMOUNT
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
         * @see PostLtCsvPaymentRowModel::INPUT_KEY_CURRENCY
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
         * @see PostLtCsvPaymentRowModel::INPUT_KEY_BANK_TRANSFER_CODE
         */
        $metadata->addPropertyConstraints(
            self::PROPERTY_BANK_TRANSFER_CODE,
            [
                new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::INPUT_KEY_BANK_TRANSFER_CODE)]),
                new Type(
                    [
                        'type' => 'digit',
                        'message' => sprintf('[%d column] This value should be of type digit.', self::INPUT_KEY_BANK_TRANSFER_CODE),
                    ]
                ),
            ]
        );
        /**
         * @see PostLtCsvPaymentRowModel::INPUT_KEY_BANK_TRANSFER_DATE
         */
        $metadata->addPropertyConstraints(
            self::PROPERTY_BANK_TRANSFER_DATE,
            [
                new NotBlank(['message' => sprintf('[%d column] This value should not be blank.', self::INPUT_KEY_BANK_TRANSFER_DATE)]),
                new Regex(
                    [
                        'message' => sprintf('[%d column] This value is not valid. Valid formats: "yyyy.mm.dd".', self::INPUT_KEY_BANK_TRANSFER_DATE),
                        'pattern' => '/^\d{4}\.\d{2}\.\d{2}$/',
                    ]
                ),
            ]
        );
    }

    /**
     * Converts '2017.08.31' to '2017-08-31' which will be accepted by DateTime constructor.
     */
    private function fixDateStringFormat(string $dateString): string
    {
        return str_replace('.', '-', $dateString);
    }
}
