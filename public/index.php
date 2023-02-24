<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use ThomasBoom89\BundesligaLiveResults\Bundesliga;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

include '../vendor/autoload.php';

$loader = new FilesystemLoader('../templates');
$twig   = new Environment($loader);

$httpClient     = new Client();
$requestFactory = new HttpFactory();
$bundesliga     = new Bundesliga($httpClient, $requestFactory);


echo $twig->render('index.html.twig', ['results' => $bundesliga->getResults(Bundesliga\LigaType::SecondBundesliga)]);
