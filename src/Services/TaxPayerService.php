<?php

namespace Supplycart\EInvoice\Services;

use Supplycart\EInvoice\EInvoiceClient;

final class TaxPayerService
{
    private string $baseUrl = '';

    public function __construct(private EInvoiceClient $client)
    {
        $this->baseUrl = $client->getBaseUrl() . '/api/v1.0/taxpayer';
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
