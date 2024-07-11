<?php

namespace Supplycart\EInvoice\Services;

use Supplycart\EInvoice\EInvoiceClient;

final class DocumentService
{
    private string $baseUrl = '';

    public function __construct(private EInvoiceClient $client)
    {
        $this->baseUrl = $client->getBaseUrl() . '/api/v1.0/documents';
    }

    public function getDocumentDetail(string $id)
    {
        $url = $this->baseUrl . '/' . $id . '/details';

        $response = $this->client->request('GET', $url);
        return $response;
    }
}
