# Payment import from csv file from post.lt

## How to use

Checkout `\EMO\PaymentStatementParserBundle\Tests\Acceptance\PostLtCsv\ImportPostLtCsvPaymentTest` for example how to use it.

## Exported file

- [paymentImportExamplePostLt.txt](paymentImportExamplePostLt.txt)
- file encoding is ISO-8859-13, **keep it the same** when editing
- file contains whitespace at the end of lines **so keep them** (e.g. PhpStorm removes trailing whitespace when saving)
- file properties (e.g. column delimiter) - defined
  in `\EMO\PaymentStatementParserBundle\Service\PostLtCsv\PostLtCsvPaymentDeserializerService::ENCODER_SETTINGS`

## Documentation

Official documentation in [post-lt-documentation.pdf](post-lt-documentation.pdf)
