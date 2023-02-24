<?php

declare(strict_types=1);

namespace ThomasBoom89\BundesligaLiveResults\Bundesliga;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use Psr\Http\Message\ResponseInterface;
use ThomasBoom89\BundesligaLiveResults\Bundesliga\Exception\CouldNotFindElement;
use ThomasBoom89\BundesligaLiveResults\Bundesliga\Exception\MissingSwiperNode;

use function assert;
use function preg_match;

class Parser
{
    /**
     * @return Result[]
     * @throws CouldNotFindElement | MissingSwiperNode
     */
    public function getResults(ResponseInterface $response): array
    {
        $content = $this->getContent($response);
        $doc     = new DOMDocument();
        @$doc->loadHTML($content);
        $xpath = new DOMXPath($doc);

        $matches = $xpath->query('.//swiper/div/div/div/div/a');
        assert($matches instanceof DOMNodeList);

        $results = [];
        foreach ($matches as $match) {
            assert($match instanceof DOMNode);
            $results[] = $this->parseMatch($match, $xpath);
        }

        return $results;
    }

    /**
     * @throws MissingSwiperNode
     */
    public function getContent(ResponseInterface $response): string
    {
        $content = $response->getBody()->getContents();
        preg_match("/<swiper.*<\/swiper>/", $content, $matches);
        if (empty($matches)) {
            throw new MissingSwiperNode();
        }
        // because we reduce the html we need to force encoding
        return '<?xml encoding="UTF-8">' . $matches[0];
    }

    /**
     * @throws CouldNotFindElement
     */
    public function parseMatch(DOMNode $match, DOMXPath $xpath): Result
    {
        $result = new Result();
        foreach ($match->childNodes as $matchInfo) {
            if ($matchInfo instanceof DOMElement === false) {
                continue;
            }

            if ($matchInfo->childElementCount === 1) {
                $this->parseTeamName($xpath, $matchInfo, $result);
            } else {
                if ($matchInfo->textContent === 'vs') {
                    $result->hasStarted = false;
                    continue;
                }

                $this->parseScore($xpath, $matchInfo, $result);
            }
        }

        return $result;
    }


    /**
     * @throws CouldNotFindElement
     */
    public function parseTeamName(DOMXPath $xpath, DOMElement $matchInfo, Result $result): void
    {
        $domNodeList = $xpath->query('.//clublogo/img', $matchInfo);
        assert($domNodeList instanceof DOMNodeList);
        $image = $domNodeList->item(0);
        if ($image instanceof DOMElement === false) {
            throw new CouldNotFindElement();
        }
        $name = $image->getAttribute('alt');
        if ($result->homeTeam === '') {
            $result->homeTeam = $name;
        } else {
            $result->guestTeam = $name;
        }
    }

    public function parseScore(DOMXPath $xpath, DOMElement $matchInfo, Result $result): void
    {
        $points = $xpath->query('.//div', $matchInfo);
        assert($points instanceof DOMNodeList);
        $result->homeScore  = (int)($points->item(0)->textContent ?? 0);
        $result->guestScore = (int)($points->item(1)->textContent ?? 0);
    }
}
