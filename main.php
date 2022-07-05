<?php

class Element
{
    private string $name;
    private array $strongerThan = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function GetName(): string
    {
        return $this->name;
    }

    public function isStrongerThan(Element $element): void
    {
        $this->strongerThan[] = $element;
    }

    public function winsAgainst(Element $element): bool
    {
        return in_array($element, $this->strongerThan);
    }
}

class Player
{
    private string $name;
    private ?Element $chosenElement = null;
    private bool $isRealPlayer;
    private int $roundsWon;


    public function __construct(string $name, bool $isRealPlayer = false, int $roundsWon = 0)
    {
        $this->name = $name;
        $this->isRealPlayer = $isRealPlayer;
        $this->roundsWon = $roundsWon;
    }

    public function getChosenElement(): Element
    {
        return $this->chosenElement;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setChosenElement(Element $chosenElement): void
    {
        $this->chosenElement = $chosenElement;
    }

    public function isRealPlayer (): bool
    {
        return $this->isRealPlayer;
    }

    public function getRoundsWon(): int
    {
        return $this->roundsWon;
    }

    public function setRoundsWon(int $roundsWon): void
    {
        $this->roundsWon = $roundsWon;
    }

}

class Game
{
    private array $elements;

    public function __construct()
    {
        $this->gameSetUp();
    }

    public function showElements(): void
    {
        /** @var Element $element*/
        foreach ($this->elements as $key =>$element) {
            echo "$key - {$element->GetName()}" . PHP_EOL;
        }
    }

    private function gameSetUp ()
    {
        $this->elements = [
            $rock = new Element('Rock'),
            $paper = new Element('Paper'),
            $scissors = new Element('Scissors'),
            $lizard = new Element('Lizard'),
            $spock = new Element('Scissors'),
            $tnt = new Element('TNT')
        ];

        $rock->isStrongerThan($scissors);
        $rock->isStrongerThan($lizard);
        $scissors->isStrongerThan($lizard);
        $scissors->isStrongerThan($paper);
        $paper->isStrongerThan($spock);
        $paper->isStrongerThan($scissors);
        $lizard->isStrongerThan($paper);
        $lizard->isStrongerThan($spock);
        $spock->isStrongerThan($scissors);
        $spock->isStrongerThan($rock);
        $tnt->isStrongerThan($rock);
        $tnt->isStrongerThan($paper);
        $tnt->isStrongerThan($scissors);
        $tnt->isStrongerThan($lizard);
        $tnt->isStrongerThan($spock);
    }

    public function startGame (Player $player1, Player $player2)
    {
        $player1->setRoundsWon(0);
        $player2->setRoundsWon(0);

        $this->showElements();


        while ($player1->getRoundsWon() !== 2 && $player2->getRoundsWon() !== 2){

            if ($player1->isRealPlayer()) {
                $player1SelectedIndex = (int)readline("{$player1->getName()}, choose your element: ");
            } else {
                $player1SelectedIndex = rand(0, count($this->elements) - 1);
            }

            $player1->setChosenElement($this->elements[$player1SelectedIndex]);
            echo "{$player1->getName()} chose {$player1->getChosenElement()->GetName()}" . PHP_EOL;

            $player2SelectedIndex = rand(0, count($this->elements) - 1);
            $player2->setChosenElement($this->elements[$player2SelectedIndex]);
            echo "{$player2->getName()} chose {$player2->getChosenElement()->GetName()}" . PHP_EOL;

            if ($player1->getChosenElement()->winsAgainst($player2->getChosenElement())) {
                echo "{$player1->getName()}  won!" . PHP_EOL;
                $player1->setRoundsWon($player1->getRoundsWon() + 1);
            } else if ($player2->getChosenElement()->winsAgainst($player1->getChosenElement())) {
                echo "{$player2->getName()}  won!" . PHP_EOL;
                $player2->setRoundsWon($player2->getRoundsWon() + 1);
            } else {
                echo "It's a TIE" . PHP_EOL;

            }

            echo "{$player1->getName()} has {$player1->getRoundsWon()} wins." . PHP_EOL;
            echo "{$player2->getName()} has {$player2->getRoundsWon()} wins." . PHP_EOL . PHP_EOL;
//            sleep(1);
        }

        echo $player1->getRoundsWon() > $player2->getRoundsWon() ?
            "{$player1->getName()} won match" : "{$player2->getName()} won match";

        echo PHP_EOL . "---------------------" . PHP_EOL;
    }
}

class Tournament
{
    private array $players;
    private array $rankings = [];

    public function __construct()
    {
        $this->setUpTournament();
    }

    public function getPlayers(): array
    {
        return $this->players;
    }

    public function printRankings(): void
    {
        /** @var Player $player*/
        foreach ($this->rankings as $key=>$player) {
            echo "#" .  (count($this->rankings) - $key + 1) . " - " .  $player->getName() . PHP_EOL;
        }
        /** @var Player $player*/
        foreach ($this->players as $key=>$player) {
            echo "#1" . " - " .  $player->getName() . PHP_EOL;
        }

    }

    private function setUpTournament ()
    {
        $this->players = [
            new Player("Martins", true),
            new Player('CPU 1'),
            new Player('CPU 2'),
            new Player('CPU 3'),
            new Player('CPU 4'),
            new Player('CPU 5'),
            new Player('CPU 6'),
            new Player('CPU 7'),
        ];
    }

    public function filterWinners (): array
    {
        $this->players = array_merge($this->players);

        /** @var Player $player*/
        foreach ($this->players as $player) {
            if (count($this->players) !== 1 && $player->getRoundsWon() !== 2) {
                $this->rankings[] = $player;
            }

        }
        $this->players = array_filter($this->getPlayers(), function (Player $player) {
            return $player->getRoundsWon() == 2;
        });
        $this->players = array_merge($this->players);
        return $this->players;
    }

    public function startTournament (Game $game)
    {
        while (count($this->players) !== 1) {
            for ($i = 0; $i < count($this->players) - 1; $i += 2) {
                $game->startGame($this->players[$i], $this->players[$i + 1]);
            }
            $this->filterWinners();
            echo " T H I S   I S   N E X T   S T A G E ! ! !" . PHP_EOL;
        }
    }
}

$testTournament = new Tournament();
$testTournament->startTournament(new Game());
$testTournament->printRankings();


