To install, run:

```zsh
$ composer require klsheng/myinvois-php-sdk "*"
```

## Usage with Laravel (tested on Laravel 11)

#### Login as Taxpayer System
```php
use Supplycart\EInvoice\EInvoiceClient;
use Illuminate\Support\Facades\Cache;

$isProd = false;
$clientId = '5b75ce93-a6d1-4e02-943f-a24ffa1b46ec';
$clientSecret = 'e4d2fcc8-90a8-40cd-99be-08f3401fbb93';

$client = new EInvoiceClient(
    clientId: $clientId,
    clientSecret: $clientSecret,
    isProd: isProd
);

$client->login();

$accessToken = $client->getAccessToken();

//Alternative to store in cache and get it later
Cache::remember('some-key', $accessToken, 3600) // expires in 3600s (1hour)

// To reuse the cached accessToken
$client->setAccessToken(Cache::get('some-key'));
```

#### Login as Intermediary System
```php
// same as login, just need to set $onBehalfOf which is the entity's TIN
$client->setOnBehalfOf($onBehalfOf); 
```

#### Validate Taxpayer's TIN
```php
$tin = 'C1234567890';
$idType = 'BRN';
$idValue = '1234567890';

$response = $client->validateTaxPayerTin(
    tin: $tin,
    idType: $idType,
    idValue: $idValue
);
```

#### Get Document Details
```php
$documentUuid = '5a282618-8ad0-4ad6-bc1d-7f2a326562cf';

$response = $client->getDocumentDetail($documentUuid);
```



