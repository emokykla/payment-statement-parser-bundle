<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Service\Csv;

use function is_array;

class CsvSerializerUtils
{
    /**
     * Csv decoder returns on first row array instead of multi-dimensional array when there is only one string in csv.
     *
     * @param string[]|string[][] $rows
     *
     * @return string[][]
     */
    public static function ensureArrayOfArrays(array $rows): array
    {
        if (!is_array($rows[0])) {
            /** @var string[][] $newRows */
            $newRows = [$rows];

            return $newRows;
        }

        /** @var string[][] $newRows */
        $newRows = $rows;

        return $newRows;
    }
}
