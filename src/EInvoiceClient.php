<?php

namespace Supplycart\EInvoice;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Psr\Http\Client\ClientInterface;
use Supplycart\EInvoice\Enums\OAuthGrantType;
use Supplycart\EInvoice\Enums\OAuthScope;
use Supplycart\EInvoice\Enums\Operation;
use Supplycart\EInvoice\Services\DocumentService;
use Supplycart\EInvoice\Services\IdentityService;
use Supplycart\EInvoice\Services\TaxPayerService;

/**
 * @method array login(?string $onBehalfOf = null, ?OAuthGrantType $grantType = OAuthGrantType::CLIENT_CREDENTIALS, ?OAuthScope $scope = OAuthScope::InvoicingAPI)
 * @method void setOnBehalfOf(string $onBehalfOf)
 * @method mixed validateTaxPayerTin(string $tin, string $idType, string $idValue)
 * @method mixed getDocumentDetail(string $id)
 */
final class EInvoiceClient
{
    public const SANDBOX_BASE_URL = 'https://preprod-api.myinvois.hasil.gov.my';
    public const PRODUCTION_BASE_URL = 'https://api.myinvois.hasil.gov.my';

    private ClientInterface $httpClient;
    private array $options = [];
    private string $baseUrl;

    /** Services */
    private ?IdentityService $identityService = null;
    private ?TaxPayerService $taxPayerService = null;
    private ?DocumentService $documentService = null;

    public function __construct(
        private string $clientId,
        private string $clientSecret,
        private bool $isProd = false,
    ) {
        $this->httpClient = new Client();

        $this->baseUrl = $isProd ? self::PRODUCTION_BASE_URL : self::SANDBOX_BASE_URL;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getIsProd(): bool
    {
        return $this->isProd;
    }

    public function getHttpClient(): Client
    {
        return $this->httpClient;
    }

    public function request(string $method, string $url, array $options = [])
    {
        $body = '';
        $options = array_merge($this->getOptions(), $options);

        try {
            $promise = $this->getHttpClient()->requestAsync($method, $url, $options);
            $response = $promise->wait();
            $body = json_decode($response->getBody(), true, JSON_FORCE_OBJECT);
        } catch (BadResponseException $exception) {
            $body = $this->handleError($exception);
        } catch (Exception $exception) {
            $this->handleError($exception);
        }

        return $body;
    }

    protected function handleError(Exception $e)
    {
        $response = $e instanceof BadResponseException ? $e->getResponse() : null;
        if ($response) {
            $body = $response->getBody()->getContents();
            $errorCode = $response->getStatusCode();
            throw new Exception($body, $errorCode);
        } else {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getOption($key): mixed
    {
        if (isset($this->options[$key])) {
            return $this->options[$key];
        }

        return false;
    }

    public function setOptions(array $options): array
    {
        return $this->options = $options;
    }

    public function setOption($key, $value)
    {
        return $this->options[$key] = $value;
    }

    public function __call($operation, mixed $args): mixed
    {
        $operationEnum = Operation::tryFrom($operation);

        if ($operationEnum === null) {
            throw new \BadMethodCallException("$operation does not exist!");
        }

        return match ($operationEnum) {
            Operation::LOGIN,
            Operation::SET_ACCESS_TOKEN,
            Operation::GET_ACCESS_TOKEN,
            Operation::SET_ON_BEHALF_OF => $this->getIdentityService()->{$operationEnum->value}(...$args),

            Operation::VALIDATE_TAX_PAYER_TIN => $this->getTaxPayerService()->{$operationEnum->value}(...$args),

            Operation::GET_DOCUMENT_DETAIL => $this->getDocumentService()->{$operationEnum->value}(...$args),

            default => throw new \BadMethodCallException($operationEnum->value . ' does not exist!')
        };
    }

    private function getIdentityService(): IdentityService
    {
        if ($this->identityService) {
            return $this->identityService;
        }

        return new IdentityService($this);
    }

    private function getTaxPayerService(): TaxPayerService
    {
        if ($this->taxPayerService) {
            return $this->taxPayerService;
        }

        return new TaxPayerService($this);
    }

    private function getDocumentService(): DocumentService
    {
        if ($this->documentService) {
            return $this->documentService;
        }

        return new DocumentService($this);
    }
}
