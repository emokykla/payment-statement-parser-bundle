<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Factory\PostLtCsv;

use EMO\PaymentStatementParserBundle\Model\PostLtCsv\PostLtCsvPaymentRowModel;

class PostLtCsvPaymentRowModelFactory
{
    /**
     * @param string[] $row
     */
    public function get(string $lineId, array $row, string $sourceString): PostLtCsvPaymentRowModel
    {
        return new PostLtCsvPaymentRowModel($lineId, $row, $sourceString);
    }
}
