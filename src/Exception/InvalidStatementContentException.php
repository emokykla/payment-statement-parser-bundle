<?php

declare(strict_types=1);

namespace EMO\PaymentStatementParserBundle\Exception;

use UnexpectedValueException;

/**
 * Thrown when payment statements can not be parsed to the validatable state.
 * E.g. number of columns in csv does not match parsing rules.
 * Or value that determines statement type is unexpected and statement type for validation can not be determined.
 */
class InvalidStatementContentException extends UnexpectedValueException
{

}
