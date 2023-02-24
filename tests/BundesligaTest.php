<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use Http\Mock\Client;
use PHPUnit\Framework\TestCase;
use ThomasBoom89\BundesligaLiveResults\Bundesliga;

class BundesligaTest extends TestCase
{
    public function testValid(): void
    {
        $bundesliga = $this->createBundesligaMock($this->getValidMockBody(), 200);
        $results    = $bundesliga->getResults(Bundesliga\LigaType::SecondBundesliga);
        $this->assertEquals($this->getValidMockResponse(), $results);
    }

    public function testInvalid(): void
    {
        $bundesliga = $this->createBundesligaMock('', 404);
        $this->expectException(Bundesliga\Exception\BadResponse::class);
        $bundesliga->getResults(Bundesliga\LigaType::SecondBundesliga);
    }

    public function testMissingImgTag(): void
    {
        $bundesliga = $this->createBundesligaMock($this->getMissingImgMockBody(), 200);
        $this->expectException(Bundesliga\Exception\CouldNotFindElement::class);
        $bundesliga->getResults(Bundesliga\LigaType::SecondBundesliga);
    }

    public function testMissingSwiperTag(): void
    {
        $bundesliga = $this->createBundesligaMock($this->getMissingSwiperNodeMockBody(), 200);
        $this->expectException(Bundesliga\Exception\MissingSwiperNode::class);
        $bundesliga->getResults(Bundesliga\LigaType::SecondBundesliga);
    }

    public function testHasNotStarted(): void
    {
        $bundesliga = $this->createBundesligaMock($this->getTextContentVSMockBody(), 200);

        $expectedResult             = new Bundesliga\Result();
        $expectedResult->homeTeam   = 'Fortuna Düsseldorf';
        $expectedResult->guestTeam  = 'Eintracht Braunschweig';
        $expectedResult->homeScore  = 0;
        $expectedResult->guestScore = 0;
        $expectedResult->hasStarted = false;
        $expectedResult->isFinished = false;

        $results = $bundesliga->getResults(Bundesliga\LigaType::SecondBundesliga);
        $this->assertEquals([$expectedResult], $results);
    }

    private function createBundesligaMock(string $mockBody, int $statusCode = 200): Bundesliga
    {
        $httpClient = new Client();
        $response   = new Response($statusCode, [], $mockBody);
        $httpClient->addResponse($response);
        $requestFactory = new HttpFactory();

        return new Bundesliga($httpClient, $requestFactory);
    }

    /**
     * @return Bundesliga\Result[]
     */
    private function getValidMockResponse(): array
    {
        $results = [];

        $first             = new Bundesliga\Result();
        $first->homeTeam   = 'DSC Arminia Bielefeld';
        $first->guestTeam  = 'F.C. Hansa Rostock';
        $first->homeScore  = 0;
        $first->guestScore = 1;
        $first->hasStarted = true;
        $first->isFinished = false;
        $results[]         = $first;

        $second             = new Bundesliga\Result();
        $second->homeTeam   = 'Karlsruher SC';
        $second->guestTeam  = 'SpVgg Greuther Fürth';
        $second->homeScore  = 2;
        $second->guestScore = 1;
        $second->hasStarted = true;
        $second->isFinished = false;
        $results[]          = $second;

        $third             = new Bundesliga\Result();
        $third->homeTeam   = 'Hannover 96';
        $third->guestTeam  = 'SC Paderborn 07';
        $third->homeScore  = 3;
        $third->guestScore = 4;
        $third->hasStarted = true;
        $third->isFinished = false;
        $results[]         = $third;

        return $results;
    }

    private function getValidMockBody(): string
    {
        return '<swiper _ngcontent-sc135="" class="swiper"><!----><!----><!----><div class="swiper-wrapper"><!----><!----><div data-swiper-slide-index="0" class="swiper-slide ng-star-inserted"><!----><div _ngcontent-sc135="" class="tile noEpg ng-star-inserted"><div _ngcontent-sc135="" class="tile__matchDate"><span _ngcontent-sc135="" class="matchKickOffDate d-none d-block snap"><span _ngcontent-sc135="">10.02. 17:30</span></span></div><div _ngcontent-sc135="" class="tile__matchInfos"><a _ngcontent-sc135="" analyticson="click" analyticsaction="Match" analyticscategory="Match Bar" class="tile__match" href="/de/2bundesliga/spieltag/2022-2023/20/dsc-arminia-bielefeld-vs-fc-hansa-rostock"><!----><div _ngcontent-sc135="" class="tile__teamLogo"><clublogo _ngcontent-sc135="" _nghost-sc113=""><img _ngcontent-sc113="" class="logo ng-star-inserted" alt="DSC Arminia Bielefeld" width="35" height="35" loading="lazy" fetchpriority="auto" src="https://img.bundesliga.com/tachyon/sites/2/2021/08/Bielefeld.png?fit=70,70"><!----><!----><!----><!----><!----></clublogo></div><!----><div _ngcontent-sc135="" class="tile__score__container ng-star-inserted"><div _ngcontent-sc135="" class="tile__score__final">0</div><div _ngcontent-sc135="" class="tile__score__final">1</div></div><!----><!----><!----><div _ngcontent-sc135="" class="tile__teamLogo"><clublogo _ngcontent-sc135="" _nghost-sc113=""><img _ngcontent-sc113="" class="logo ng-star-inserted" alt="F.C. Hansa Rostock" width="35" height="35" loading="lazy" fetchpriority="auto" src="https://img.bundesliga.com/tachyon/sites/2/2021/08/Rostock.png?fit=70,70"><!----><!----><!----><!----><!----></clublogo></div><!----></a><!----><div _ngcontent-sc135="" class="tile__epg animated-background ng-star-inserted"><div _ngcontent-sc135="" class="epg-loading animated-background"><div _ngcontent-sc135="" class="epg ng-star-inserted"><div _ngcontent-sc135="" class="match"></div></div><!----></div></div><!----></div></div><!----><!----><!----></div><div data-swiper-slide-index="1" class="swiper-slide ng-star-inserted"><!----><div _ngcontent-sc135="" class="tile noEpg ng-star-inserted"><div _ngcontent-sc135="" class="tile__matchDate"><span _ngcontent-sc135="" class="matchKickOffDate d-none"><span _ngcontent-sc135="">10.02. 17:30</span></span></div><div _ngcontent-sc135="" class="tile__matchInfos"><a _ngcontent-sc135="" analyticson="click" analyticsaction="Match" analyticscategory="Match Bar" class="tile__match" href="/de/2bundesliga/spieltag/2022-2023/20/karlsruher-sc-vs-spvgg-greuther-fuerth"><!----><div _ngcontent-sc135="" class="tile__teamLogo"><clublogo _ngcontent-sc135="" _nghost-sc113=""><img _ngcontent-sc113="" class="logo ng-star-inserted" alt="Karlsruher SC" width="35" height="35" loading="lazy" fetchpriority="auto" src="https://img.bundesliga.com/tachyon/sites/2/2021/08/Karlsruhe.png?fit=70,70"><!----><!----><!----><!----><!----></clublogo></div><!----><div _ngcontent-sc135="" class="tile__score__container ng-star-inserted"><div _ngcontent-sc135="" class="tile__score__final">2</div><div _ngcontent-sc135="" class="tile__score__final">1</div></div><!----><!----><!----><div _ngcontent-sc135="" class="tile__teamLogo"><clublogo _ngcontent-sc135="" _nghost-sc113=""><img _ngcontent-sc113="" class="logo ng-star-inserted" alt="SpVgg Greuther Fürth" width="35" height="35" loading="lazy" fetchpriority="auto" src="https://img.bundesliga.com/tachyon/sites/2/2021/08/Fuerth.png?fit=70,70"><!----><!----><!----><!----><!----></clublogo></div><!----></a><!----><div _ngcontent-sc135="" class="tile__epg animated-background ng-star-inserted"><div _ngcontent-sc135="" class="epg-loading animated-background"><div _ngcontent-sc135="" class="epg ng-star-inserted"><div _ngcontent-sc135="" class="match"></div></div><!----></div></div><!----></div></div><!----><!----><!----></div><div data-swiper-slide-index="4" class="swiper-slide ng-star-inserted"><!----><div _ngcontent-sc135="" class="tile noEpg ng-star-inserted"><div _ngcontent-sc135="" class="tile__matchDate"><span _ngcontent-sc135="" class="matchKickOffDate d-none"><span _ngcontent-sc135="">11.02. 12:00</span></span></div><div _ngcontent-sc135="" class="tile__matchInfos"><a _ngcontent-sc135="" analyticson="click" analyticsaction="Match" analyticscategory="Match Bar" class="tile__match" href="/de/2bundesliga/spieltag/2022-2023/20/hannover-96-vs-sc-paderborn-07"><!----><div _ngcontent-sc135="" class="tile__teamLogo"><clublogo _ngcontent-sc135="" _nghost-sc113=""><img _ngcontent-sc113="" class="logo ng-star-inserted" alt="Hannover 96" width="35" height="35" loading="lazy" fetchpriority="auto" src="https://img.bundesliga.com/tachyon/sites/2/2021/08/Hannover.png?fit=70,70"><!----><!----><!----><!----><!----></clublogo></div><!----><div _ngcontent-sc135="" class="tile__score__container ng-star-inserted"><div _ngcontent-sc135="" class="tile__score__final">3</div><div _ngcontent-sc135="" class="tile__score__final">4</div></div><!----><!----><!----><div _ngcontent-sc135="" class="tile__teamLogo"><clublogo _ngcontent-sc135="" _nghost-sc113=""><img _ngcontent-sc113="" class="logo ng-star-inserted" alt="SC Paderborn 07" width="35" height="35" loading="lazy" fetchpriority="auto" src="https://img.bundesliga.com/tachyon/sites/2/2022/06/Paderborn-SCP.png?fit=70,70"><!----><!----><!----><!----><!----></clublogo></div><!----></a><!----><div _ngcontent-sc135="" class="tile__epg animated-background ng-star-inserted"><div _ngcontent-sc135="" class="epg-loading animated-background"><div _ngcontent-sc135="" class="epg ng-star-inserted"><div _ngcontent-sc135="" class="match"></div></div><!----></div></div><!----></div></div><!----><!----><!----></div><!----><!----><!----><!----></div><!----></swiper>';
    }

    private function getTextContentVSMockBody(): string
    {
        return '<swiper _ngcontent-sc135="" class="swiper"><!----><!----><!----><div class="swiper-wrapper"><!----><!----><div data-swiper-slide-index="0" class="swiper-slide ng-star-inserted"><!----><div _ngcontent-sc135="" class="tile ng-star-inserted"><div _ngcontent-sc135="" class="tile__matchDate"><span _ngcontent-sc135="" class="matchKickOffDate d-none d-block snap"><span _ngcontent-sc135="">24.02. 17:30</span></span></div><div _ngcontent-sc135="" class="tile__matchInfos"><a _ngcontent-sc135="" analyticson="click" analyticsaction="Match" analyticscategory="Match Bar" class="tile__match" href="/de/2bundesliga/spieltag/2022-2023/22/fortuna-duesseldorf-vs-eintracht-braunschweig"><!----><div _ngcontent-sc135="" class="tile__teamLogo"><clublogo _ngcontent-sc135="" _nghost-sc113=""><img _ngcontent-sc113="" class="logo ng-star-inserted" alt="Fortuna Düsseldorf" width="35" height="35" loading="lazy" fetchpriority="auto" src="https://img.bundesliga.com/tachyon/sites/2/2021/08/Duesseldorf.png?fit=70,70"><!----><!----><!----><!----><!----></clublogo></div><div _ngcontent-sc135="" class="tile__score__scheduled ng-star-inserted">vs</div><!----><!----><!----><!----><div _ngcontent-sc135="" class="tile__teamLogo"><clublogo _ngcontent-sc135="" _nghost-sc113=""><img _ngcontent-sc113="" class="logo ng-star-inserted" alt="Eintracht Braunschweig" width="35" height="35" loading="lazy" fetchpriority="auto" src="https://img.bundesliga.com/tachyon/sites/2/2022/06/Braunschweig-EBS.png?fit=70,70"><!----><!----><!----><!----><!----></clublogo></div><!----></a><!----><div _ngcontent-sc135="" class="tile__epg animated-background ng-star-inserted"><div _ngcontent-sc135="" class="epg-loading animated-background"><div _ngcontent-sc135="" class="epg ng-star-inserted"><div _ngcontent-sc135="" class="match"></div></div><!----></div></div><!----></div></div><!----><!----><!----></div></div><!----></div></div><!----></div></div><!----><!----><!----></div><!----><!----><!----><!----></div><!----></swiper>';
    }

    private function getMissingImgMockBody(): string
    {
        return '<swiper _ngcontent-sc135="" class="swiper"><!----><!----><!----><div class="swiper-wrapper"><!----><!----><div data-swiper-slide-index="0" class="swiper-slide ng-star-inserted"><!----><div _ngcontent-sc135="" class="tile ng-star-inserted"><div _ngcontent-sc135="" class="tile__matchDate"><span _ngcontent-sc135="" class="matchKickOffDate d-none d-block snap"><span _ngcontent-sc135="">24.02. 17:30</span></span></div><div _ngcontent-sc135="" class="tile__matchInfos"><a _ngcontent-sc135="" analyticson="click" analyticsaction="Match" analyticscategory="Match Bar" class="tile__match" href="/de/2bundesliga/spieltag/2022-2023/22/fortuna-duesseldorf-vs-eintracht-braunschweig"><!----><div _ngcontent-sc135="" class="tile__teamLogo"><clublogo _ngcontent-sc135="" _nghost-sc113=""></clublogo></div><div _ngcontent-sc135="" class="tile__score__scheduled ng-star-inserted">vs</div><!----><!----><!----><!----><div _ngcontent-sc135="" class="tile__teamLogo"><clublogo _ngcontent-sc135="" _nghost-sc113=""><img _ngcontent-sc113="" class="logo ng-star-inserted" alt="Eintracht Braunschweig" width="35" height="35" loading="lazy" fetchpriority="auto" src="https://img.bundesliga.com/tachyon/sites/2/2022/06/Braunschweig-EBS.png?fit=70,70"><!----><!----><!----><!----><!----></clublogo></div><!----></a><!----><div _ngcontent-sc135="" class="tile__epg animated-background ng-star-inserted"><div _ngcontent-sc135="" class="epg-loading animated-background"><div _ngcontent-sc135="" class="epg ng-star-inserted"><div _ngcontent-sc135="" class="match"></div></div><!----></div></div><!----></div></div><!----><!----><!----></div></div><!----></div></div><!----></div></div><!----><!----><!----></div><!----><!----><!----><!----></div><!----></swiper>';
    }

    private function getMissingSwiperNodeMockBody(): string
    {
        return '<div></div>';
    }
}
