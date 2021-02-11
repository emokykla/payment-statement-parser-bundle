<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Service\Csv;

use EMO\PaymentStatementParserBundle\Model\Csv\CsvRowModel;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

use function rtrim;

class CsvDeserializerService
{
    private const ENCODER_FORMAT = CsvEncoder::FORMAT;

    /** @var CsvEncoder */
    private $csvEncoder;

    public function __construct()
    {
        $this->csvEncoder = new CsvEncoder();
    }

    /**
     * @param mixed[] $encoderSettings
     *
     * @return CsvRowModel[]
     */
    public function explodePaymentsCsv(string $content, array $encoderSettings): array
    {
        $rows = $this->csvEncoder->decode($content, self::ENCODER_FORMAT, $encoderSettings);

        /** @var CsvRowModel[] $csvRowModels */
        $csvRowModels = [];
        foreach ($rows as $key => $row) {
            /* since it is difficult to get original string from csv because it may contain new lines in data, let's use encode to create new csv string */
            /** @var string $sourceString */
            $sourceString = $this->csvEncoder->encode($row, self::ENCODER_FORMAT, $encoderSettings);
            /* `csvEncoder->encode` add new line at the end. Remove it. */
            $sourceString = rtrim($sourceString);

            $csvRowModels[] = new CsvRowModel($key + 1, $row, $sourceString);
        }

        return $csvRowModels;
    }
}
