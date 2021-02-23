<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Service\SwedbankCsv;

use EMO\PaymentStatementParserBundle\Model\SwedbankCsv\AbstractSwedbankCsvPaymentRowModel;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SwedbankCsvPaymentValidatorService
{
    /** @var ValidatorInterface */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validatePaymentRow(AbstractSwedbankCsvPaymentRowModel $rowModel): ConstraintViolationListInterface
    {
        $constraintViolationList = $this->validator
            ->startContext()
            ->atPath($rowModel->getSourceLineId())
            ->validate($rowModel)
            ->getViolations()
        ;

        return $constraintViolationList;
    }
}
