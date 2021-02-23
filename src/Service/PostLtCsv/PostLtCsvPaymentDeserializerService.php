<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Service\PostLtCsv;

use EMO\PaymentStatementParserBundle\Model\Csv\CsvRowModel;
use EMO\PaymentStatementParserBundle\Service\Csv\CsvDeserializerService;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

class PostLtCsvPaymentDeserializerService
{
    private const ENCODER_SETTINGS = [
        CsvEncoder::DELIMITER_KEY => "\t",
        CsvEncoder::ENCLOSURE_KEY => '"',
        CsvEncoder::KEY_SEPARATOR_KEY => '.',
        CsvEncoder::NO_HEADERS_KEY => true,
        CsvEncoder::AS_COLLECTION_KEY => true,
    ];

    /** @var CsvDeserializerService */
    private $csvDeserializerService;

    public function __construct(CsvDeserializerService $csvDeserializerService)
    {
        $this->csvDeserializerService = $csvDeserializerService;
    }

    public function convertEncodingToUtf8(string $content): string
    {
        return mb_convert_encoding($content, 'utf-8', 'iso-8859-13');
    }

    /**
     * @param string $content make sure string comes in UTF-8 encoding {@see PostLtCsvPaymentDeserializerService::convertEncodingToUtf8}
     *
     * @return CsvRowModel[]
     */
    public function explodePaymentsCsv(string $content): array
    {
        return $this->csvDeserializerService->explodePaymentsCsv($content, self::ENCODER_SETTINGS);
    }
}
