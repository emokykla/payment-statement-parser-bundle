# Payment import from csv file from swedbank.lt

## How to use

Checkout `\EMO\PaymentStatementParserBundle\Tests\Acceptance\SwedbankCsv\ImportSwedbankCsvPaymentTest` for example how to use it.

## Exported file

- [paymentImportExampleSwedbank.csv](paymentImportExampleSwedbank.csv)
- file encoding is ISO-8859-13, **keep it the same** when editing
- file properties (e.g. column delimiter) - defined
  in `\EMO\PaymentStatementParserBundle\Service\SwedbankCsv\SwedbankCsvPaymentDeserializerService::ENCODER_SETTINGS`.

## Documentation

Documentation that seems to match actual data but taken from random site on the internet because swedbank is not able to provide this information :/

- [csv-format-documentation-screenshot-1.png](csv-format-documentation-screenshot-1.png)
- [csv-format-documentation-screenshot-2.png](csv-format-documentation-screenshot-2.png)
