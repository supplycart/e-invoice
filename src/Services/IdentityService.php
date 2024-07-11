<?php

namespace Supplycart\EInvoice\Services;

use Exception;
use Supplycart\EInvoice\EInvoiceClient;
use Supplycart\EInvoice\Enums\OAuthGrantType;
use Supplycart\EInvoice\Enums\OAuthScope;

final class IdentityService
{
    public const SANDBOX_IDENTITY_BASE_URL = 'https://preprod-api.myinvois.hasil.gov.my/connect/token';
    public const PRODUCTION_IDENTITY_BASE_URL = 'https://api.myinvois.hasil.gov.my/connect/token';

    private string $authToken = '';
    private string $baseUrl = '';

    public function __construct(private EInvoiceClient $client)
    {
        $this->baseUrl = $client->getIsProd() ? self::PRODUCTION_IDENTITY_BASE_URL : self::SANDBOX_IDENTITY_BASE_URL;
    }

    public function getAuthToken(): string
    {
        return $this->authToken;
    }

    public function setAuthToken(string $newAuthToken): void
    {
        $this->authToken = $newAuthToken;
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
