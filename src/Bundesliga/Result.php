<?php

declare(strict_types=1);

namespace ThomasBoom89\BundesligaLiveResults\Bundesliga;

class Result
{
    public string $homeTeam   = '';
    public string $guestTeam  = '';
    public int    $homeScore  = 0;
    public int    $guestScore = 0;
    public bool   $hasStarted = true;
    public bool   $isFinished = false;
}
