<?php declare(strict_types=1);

namespace CorrectHorseBattery\EventSubscribers;

use CorrectHorseBattery\Domain\Duel;
use CorrectHorseBattery\Domain\Duels;
use CorrectHorseBattery\Events\ChallengeToDuelAccepted;
use CorrectHorseBattery\Repositories\Players;

final class BeginDuelWhenChallengeToDuelAccepted
{
    private $players;
    private $duels;

    public function __construct(Players $players, Duels $duels)
    {
        $this->players = $players;
        $this->duels = $duels;
    }

    public function __invoke(ChallengeToDuelAccepted $event)
    {
        $challengingPlayer = $this->players->getByUsername($event->challengingPlayer());
        $challengedPlayer = $this->players->getByUsername($event->challengedPlayer());

        $this->duels->save(Duel::begin(
            uniqid('', true),
            $challengingPlayer->username(),
            $challengedPlayer->username()
        ));
    }
}
