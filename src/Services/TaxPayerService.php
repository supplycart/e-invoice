<?php

namespace Supplycart\EInvoice\Services;

use Supplycart\EInvoice\EInvoiceClient;

final class TaxPayerService
{
    public const SANDBOX_TAX_PAYER_API_BASE_URL = 'https://preprod-api.myinvois.hasil.gov.my/api/v1.0/taxpayer';
    public const PRODUCTION_TAX_PAYER_API_BASE_URL = 'https://api.myinvois.hasil.gov.my/api/v1.0/taxpayer';

    private string $baseUrl = '';

    public function __construct(private EInvoiceClient $client)
    {
        $this->baseUrl = $client->getIsProd() ? self::PRODUCTION_TAX_PAYER_API_BASE_URL : self::SANDBOX_TAX_PAYER_API_BASE_URL;
    }

    public function validateTaxPayerTin(string $tin, string $idType, string $idValue)
    {
        $url = $this->baseUrl . '/validate/' . $tin;

        $response = $this->client->request('GET', $url, [
            'query' => [
                'idType' => $idType,
                'idValue' => $idValue,
            ],
        ]);

        if (empty($response)) {
            return true;
        }

        return $response;
    }
}
