<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Model\SwedbankCsv;

use RuntimeException;

use function preg_match;
use function sprintf;
use function trim;

class SwedbankCsvPaymentTransactionDetailsModel
{
    /** @var string */
    private $purposeOfPayment;

    public function __construct(string $details)
    {
        /**
         * @see SwedbankCsvPaymentTransactionRowModel::INPUT_KEY_DETAILS
         */
        if (preg_match('#([^/]*)/([^/]*)/([^/]*)/([^/]*)/([^/]*)/([^/]*)/([^/]*)/([^/]*)/([^/]*)#u', $details, $matches)) {
            $this->purposeOfPayment = trim($matches[1]);
        } else {
            throw new RuntimeException(sprintf('Details string was not in correct format, expected 9 strings concatenated by "/", got "%s".', $details));
        }
    }

    public function getPurposeOfPayment(): string
    {
        return $this->purposeOfPayment;
    }
}
