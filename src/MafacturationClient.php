<?php

namespace Mafacturation\PhpClient;

use Exception;
use GuzzleHttp\Client;
use Mafacturation\PhpClient\Exceptions\InternalServerError;
use Mafacturation\PhpClient\Exceptions\NotAllowed;
use Mafacturation\PhpClient\Exceptions\NotFound;
use Mafacturation\PhpClient\Exceptions\NotValid;
use Mafacturation\PhpClient\Exceptions\PerformingMaintenance;
use Mafacturation\PhpClient\Exceptions\TooManyAttempts;
use Mafacturation\PhpClient\Exceptions\Unauthenticated;
use Mafacturation\PhpClient\Http\Response;
use Mafacturation\PhpClient\Resources\Customer;
use Psr\Http\Message\ResponseInterface;

class MafacturationClient
{
    public Client $httpClient;
    private string $url = 'https://mafacturation.be/api/';
    private string $token;

    public function __construct(?string $token = null, ?string $tenant = null)
    {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type'  => 'application/json',
        ];
        $this->httpClient = new  Client([
            'base_uri' => $this->url,
            'headers' => $headers,
        ]);
        $this->setToken($token);
        $this->setTenant($tenant);
    }

    //set token function
    public function setToken(string $token): void
    {
        $this->token = $token;
        $this->httpClient->setDefaultOption('headers/X-Auth-Token', $token);
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setTenant(string $tenant): void
    {
        $this->httpClient->setDefaultOption('headers/X-Tenant', $tenant);
    }

    public function makeAPICall(string $url, string $method = 'get', array $options = []): Response
    {
        if (!in_array($method, ['get', 'post', 'patch', 'delete'])) {
            throw new Exception('Invalid method type');
        }

        /**
         * @var ResponseInterface $response
         */
        $response = $this->httpClient->{$method}($url, $options);

        switch ($response->getStatusCode()) {
            case 401:
                throw new Unauthenticated($response->getBody());
            case 404:
                throw new NotFound($response->getBody());
            case 405:
                throw new NotAllowed($response->getBody());
            case 422:
                throw new NotValid($response->getBody());
            case 429:
                throw new TooManyAttempts($response->getBody());
            case 500:
                throw new InternalServerError($response->getBody());
            case 503:
                throw new PerformingMaintenance($response->getBody());
        }

        return new Response($response);
    }

    public function customer(int $id = null): Customer
    {
        return new Customer($this, $id);
    }

    public function customers(int $id = null): Customer
    {
        return $this->customer($id);
    }



}