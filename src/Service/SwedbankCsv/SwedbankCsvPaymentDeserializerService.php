<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Service\SwedbankCsv;

use EMO\PaymentStatementParserBundle\Model\Csv\CsvRowModel;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

use function rtrim;

class SwedbankCsvPaymentDeserializerService
{
    private const ENCODER_SETTINGS = [
        CsvEncoder::DELIMITER_KEY => ',',
        CsvEncoder::ENCLOSURE_KEY => '"',
        CsvEncoder::KEY_SEPARATOR_KEY => '.',
        CsvEncoder::NO_HEADERS_KEY => true,
        CsvEncoder::AS_COLLECTION_KEY => true,
    ];
    private const ENCODER_FORMAT = CsvEncoder::FORMAT;

    /** @var CsvEncoder */
    private $csvEncoder;

    public function __construct()
    {
        $this->csvEncoder = new CsvEncoder();
    }

    /**
     * @return CsvRowModel[]
     */
    public function explodePaymentsCsv(string $content): array
    {
        $rows = $this->csvEncoder->decode($content, self::ENCODER_FORMAT, self::ENCODER_SETTINGS);

        /** @var CsvRowModel[] $csvRowModels */
        $csvRowModels = [];
        foreach ($rows as $key => $row) {
            /* since it is difficult to get original string from csv because it may contain new lines in data, let's use encode to create new csv string */
            /** @var string $sourceString */
            $sourceString = $this->csvEncoder->encode($row, self::ENCODER_FORMAT, self::ENCODER_SETTINGS);
            /* `csvEncoder->encode` add new line at the end. Remove it. */
            $sourceString = rtrim($sourceString);

            $csvRowModels[] = new CsvRowModel($key + 1, $row, $sourceString);
        }

        return $csvRowModels;
    }
}
