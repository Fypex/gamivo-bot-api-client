<?php

namespace Fypex\GamivoClient;

use Fypex\GamivoClient\Denormalizer\Offers\OffersDenormalizer;
use Psr\Cache\InvalidArgumentException;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Fypex\GamivoClient\Credentials\GamivoCredentials;
use Fypex\GamivoClient\Exception\GeneralException;
use Fypex\GamivoClient\Request\Offers;
use Http\Client\HttpClient;
use Http\Client\Curl\Client as CurlClient;
use Http\Message\MessageFactory\DiactorosMessageFactory;
use Psr\Http\Message\ResponseInterface;
use Symfony\Contracts\Cache\ItemInterface;

class GamivoBotClient
{

    protected $url;
    protected $client;
    protected $credentials;
    protected $messageFactory;

    public function __construct(GamivoCredentials $credentials, ?HttpClient $client = null)
    {
        $this->client = $client ?: new CurlClient(null,null,[
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $this->credentials = $credentials;
        $this->messageFactory = new DiactorosMessageFactory();
    }

    public function setApiUrl($url)
    {
        $this->url = $url;
    }
    public function getApiUrl(): string
    {
       return $this->url ?? GamivoBot::DEFAULT_URL;
    }
    protected function authorization()
    {

        $body = [
            'email' => $this->credentials->getEmail(),
            'password' => $this->credentials->getPassword()
        ];

        $request = $this->messageFactory->createRequest(
            'POST',
            $this->getApiUrl().'/api/login',
            $this->getHeaders('application/json', false),
            json_encode($body)
        );

        try {
            $response = $this->client->sendRequest($request);
        }catch (\Exception $exception){
            throw new GeneralException($exception->getMessage(),$exception->getCode());
        } catch (ClientExceptionInterface $e) {
            throw new GeneralException($e->getMessage(), $e->getCode());
        }

        $data = $this->handleResponse($response);
        if (isset($data['token'])){
            return $data['token'];
        }else{
            throw new GeneralException($data);
        }


    }
    protected function getAuthorizationToken(): string
    {

        $cache = new FilesystemAdapter();

        return $cache->get('token', function (ItemInterface $item) {
            $item->expiresAfter(3600);

            $computedValue = $this->authorization();

            return $computedValue;
        });

    }
    protected function isJsonResponse(ResponseInterface $response): bool
    {
        $header = $response->getHeader('Content-Type')[0] ?? null;
        [$type,] = explode(';', $header);

        return $type === 'application/json';
    }
    protected function getHeaders($contentType = 'application/json', $authorized = true): array
    {
        $headers = [
            'Content-Type' => $contentType,
            'Accept' => '*/*',
        ];

        if ($authorized) {
            $headers['Authorization'] = 'Bearer '. $this->getAuthorizationToken();
        }
        return $headers;
    }
    protected function handleResponse(ResponseInterface $response)
    {
        if (!$this->isJsonResponse($response)) {
            throw new GeneralException('Response is not "application/json" type');
        }

        $data = json_decode((string)$response->getBody(), true);
        if ($response->getStatusCode() == 401) {
            throw new GeneralException($data['error'], $response->getStatusCode());
        }

        if ($response->getStatusCode() !== 200) {
            throw new GeneralException($data['message'], $response->getStatusCode());
        }

        return $data;
    }

    public function getOffers()
    {

        $request = $this->messageFactory->createRequest(
            'GET',
            $this->getApiUrl().'/api/offers',
            $this->getHeaders('application/json', true),
        );

        $response = $this->client->sendRequest($request);
        $data = $this->handleResponse($response);

        return (new OffersDenormalizer())->denormalize($data);
    }
}
