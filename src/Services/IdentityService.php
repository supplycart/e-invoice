<?php

namespace Supplycart\EInvoice\Services;

use Exception;
use Supplycart\EInvoice\EInvoiceClient;
use Supplycart\EInvoice\Enums\OAuthGrantType;
use Supplycart\EInvoice\Enums\OAuthScope;

final class IdentityService
{
    private string $baseUrl = '';

    public function __construct(private EInvoiceClient $client)
    {
        $this->baseUrl = $client->getBaseUrl() . '/connect/token';
    }

    public function login(
        ?string $onBehalfOf = null,
        ?OAuthGrantType $grantType = OAuthGrantType::CLIENT_CREDENTIALS,
        ?OAuthScope $scope = OAuthScope::InvoicingAPI
    ) {
        if (!is_null($onBehalfOf)) {
            $this->setOnbehalfof($onBehalfOf);
        }

        $body = [
            'form_params' => [
                'client_id' => $this->client->getClientId(),
                'client_secret' => $this->client->getClientSecret(),
                'grant_type' => $grantType->value,
                'scope' => $scope->value,
            ],
        ];

        $response = $this->client->request('POST', $this->baseUrl, $body);

        if (!is_array($response) || !array_key_exists('access_token', $response)) {
            throw new Exception('access_token not found!');
        }

        $this->setAccessToken($response['access_token']);

        return $response;
    }

    private function setOnBehalfOf(string $onBehalfOf)
    {
        $headers = $this->client->getOption('headers');

        if(!$headers) {
            $headers = [];
        }
        $headers = array_merge($headers, [
            'onbehalfof' => $onBehalfOf,
        ]);

        return $this->client->setOption('headers', $headers);
    }

    private function setAccessToken($token)
    {
        $headers = $this->client->getOption('headers');
        if(!$headers) {
            $headers = [];
        }
        $headers = array_merge($headers, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        return $this->client->setOption('headers', $headers);
    }
}
