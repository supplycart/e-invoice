<?php

namespace Supplycart\EInvoice\Services;

use Supplycart\EInvoice\EInvoiceClient;

final class DocumentService
{
    public const SANDBOX_TAX_PAYER_API_BASE_URL = 'https://preprod-api.myinvois.hasil.gov.my/api/v1.0/documents';
    public const PRODUCTION_TAX_PAYER_API_BASE_URL = 'https://api.myinvois.hasil.gov.my/api/v1.0/documents';

    private string $baseUrl = '';

    public function __construct(private EInvoiceClient $client)
    {
        $this->baseUrl = $client->getIsProd() ? self::PRODUCTION_TAX_PAYER_API_BASE_URL : self::SANDBOX_TAX_PAYER_API_BASE_URL;
    }

    public function getDocumentDetail(string $id)
    {
        $url = $this->baseUrl . '/' . $id . '/details';

        $response = $this->client->request('GET', $url);
        return $response;
    }
}
