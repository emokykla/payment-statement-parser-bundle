<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Model\Csv;

class CsvRowModel
{
    /** @var int */
    private $lineNo;
    /** @var string[] */
    private $row;
    /** @var string */
    private $source;

    /**
     * @param string[] $row
     */
    public function __construct(int $lineNo, array $row, string $source)
    {
        $this->lineNo = $lineNo;
        $this->row = $row;
        $this->source = $source;
    }

    public function getLineNo(): int
    {
        return $this->lineNo;
    }

    /**
     * @return string[]
     */
    public function getRow(): array
    {
        return $this->row;
    }

    public function getSource(): string
    {
        return $this->source;
    }
}
