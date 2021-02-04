<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Service\SwedbankCsv;

use EMO\PaymentStatementParserBundle\Service\Csv\CsvSerializerUtils;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

class SwedbankCsvPaymentDeserializerService
{
    /** @var CsvEncoder */
    private $csvEncoder;

    public function __construct()
    {
        $this->csvEncoder = new CsvEncoder();
    }

    /**
     * @return string[][]
     */
    public function explodePaymentsCsv(string $content): array
    {
        $contentArray = $this->csvEncoder->decode(
            $content,
            CsvEncoder::FORMAT,
            [
                CsvEncoder::DELIMITER_KEY => ',',
                CsvEncoder::ENCLOSURE_KEY => '"',
                CsvEncoder::KEY_SEPARATOR_KEY => '.',
                CsvEncoder::NO_HEADERS_KEY => true,
            ]
        );

        $contentArray = CsvSerializerUtils::ensureArrayOfArrays($contentArray);

        return $contentArray;
    }
}
