<?php

declare(strict_types=1);

namespace ThomasBoom89\BundesligaLiveResults;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use ThomasBoom89\BundesligaLiveResults\Bundesliga\Exception\BadResponse;
use ThomasBoom89\BundesligaLiveResults\Bundesliga\Exception\CouldNotFindElement;
use ThomasBoom89\BundesligaLiveResults\Bundesliga\Exception\MissingSwiperNode;
use ThomasBoom89\BundesligaLiveResults\Bundesliga\LigaType;
use ThomasBoom89\BundesligaLiveResults\Bundesliga\Parser;
use ThomasBoom89\BundesligaLiveResults\Bundesliga\Result;

class Bundesliga
{
    private Parser $parser;

    public function __construct(
        private readonly ClientInterface         $httpClient,
        private readonly RequestFactoryInterface $requestFactory
    ) {
        $this->parser = new Parser();
    }

    /**
     * @return Result[]
     * @throws BadResponse
     * @throws CouldNotFindElement | MissingSwiperNode
     * @throws ClientExceptionInterface
     */
    public function getResults(LigaType $liga): array
    {
        $url      = $this->buildUrl($liga);
        $request  = $this->requestFactory->createRequest('GET', $url);
        $response = $this->httpClient->sendRequest($request);

        if ($response->getStatusCode() !== 200) {
            throw new BadResponse($response->getStatusCode() . $response->getReasonPhrase());
        }

        return $this->parser->getResults($response);
    }

    private function buildUrl(LigaType $liga): string
    {
        return match ($liga) {
            LigaType::FirstBundesliga  => 'https://www.bundesliga.com/de/bundesliga/liveticker-ergebnisse-tabelle',
            LigaType::SecondBundesliga => 'https://www.bundesliga.com/de/2bundesliga/liveticker-ergebnisse-tabelle',
        };
    }
}
