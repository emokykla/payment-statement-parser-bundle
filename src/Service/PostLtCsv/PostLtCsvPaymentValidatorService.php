<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Service\PostLtCsv;

use EMO\PaymentStatementParserBundle\Model\PostLtCsv\PostLtCsvPaymentRowModel;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PostLtCsvPaymentValidatorService
{
    /** @var ValidatorInterface */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validatePaymentRow(PostLtCsvPaymentRowModel $rowModel): ConstraintViolationListInterface
    {
        $constraintViolationList = $this->validator
            ->startContext()
            ->atPath($rowModel->getLineId())
            ->validate($rowModel)
            ->getViolations()
        ;

        return $constraintViolationList;
    }
}
