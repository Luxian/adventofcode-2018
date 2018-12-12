<?php /** @noinspection AutoloadingIssuesInspection */

$tests = [
    ['players' =>  9, 'marble' =>   25, 'winning_score' => 32],
    ['players' => 10, 'marble' => 1618, 'winning_score' => 8317],
    ['players' => 13, 'marble' => 7999, 'winning_score' => 146373],
    ['players' => 17, 'marble' => 1104, 'winning_score' => 2764],
    ['players' => 21, 'marble' => 6111, 'winning_score' => 54718],
    ['players' => 30, 'marble' => 5807, 'winning_score' => 37305],
];

foreach($tests as $test_no => $test) {
    $game = new MarbleGame($test['players'], $test['marble']);
    $game->play();
    echo 'Test #' . ($test_no + 1) . ": {$test['players']} players, last marble worth {$test['marble']}. => ";
    if ($game->getWinningScore() !== $test['winning_score']) {
        echo 'FAILED' . PHP_EOL;
        echo "Expected winning score {$test['winning_score']} and got " . $game->getWinningScore() . PHP_EOL;
        exit(1);
    }
    echo 'PASSED' . PHP_EOL;
}


$game = new MarbleGame(459, 72103);
$game->play();
echo 'Winning score is: ' . $game->getWinningScore() . PHP_EOL;
echo 'Winning elf is: ' . $game->getWinningElf() . PHP_EOL;


class MarbleGame
{

    /** @var int */
    private $number_of_players;

    /** @var int */
    private $last_marble_worth;

    /** @var int[] */
    private $circle = [0];

    /** @var  int[] */
    private $scores = [];

    /** @var int */
    private $current_player = 0;

    /** @var int */
    private $current_marble = 1;

    /** @var int */
    private $current_position = 0;

    public function __construct(int $number_of_players, int $last_marble_worth)
    {
        $this->number_of_players = $number_of_players;
        $this->last_marble_worth = $last_marble_worth;
    }

    public function play(): void
    {
        while($this->getCurrentMarble() <= $this->last_marble_worth) {
            if ($this->getCurrentMarble() % 23) {
                $this->insertNextMarble();
            }
            else {
                $position_to_remove = ($this->getCurrentPosition() + $this->getCircleSize() - 7) % $this->getCircleSize();
                $this->addPointsToCurrentPlayer($this->getCurrentMarble());
                $this->addPointsToCurrentPlayer($this->circle[$position_to_remove]);

                $this->circle = array_merge(
                    array_slice($this->circle, 0, $position_to_remove),
                    array_slice($this->circle, $position_to_remove + 1)
                );

                $this->current_position = $position_to_remove;
            }

            $this->switchPlayer();
            $this->switchMarble();
        }
        echo "\r";
    }

    public function getWinningScore(): int
    {
        return array_reduce($this->scores, function($carry, $item) { return max($item, $carry); });
    }

    public function getWinningElf(): int
    {
        $winning_score = $this->getWinningScore();
        return array_search($winning_score, $this->scores) + 1;
    }

    private function getPlayer(): int
    {
        return $this->current_player;
    }

    private function switchPlayer(): void
    {
        $this->current_player = ($this->current_player + 1) % $this->number_of_players;
    }

    private function getCircleSize(): int
    {
        return count($this->circle);
    }

    private function getCurrentPosition(): int
    {
        return $this->current_position;
    }

    private function getCurrentMarble(): int
    {
        return $this->current_marble;
    }

    private function switchMarble(): void
    {
        $this->current_marble++;
    }

    private function insertNextMarble(): void
    {
        $new_marble_position = (($this->getCurrentPosition() + 1) % $this->getCircleSize()) + 1;

        array_splice($this->circle, $new_marble_position, 0, [$this->getCurrentMarble()]);
        $this->circle = array_values($this->circle);

//        for($i = $this->getCircleSize(); $i > $new_marble_position; $i--) {
//            $this->circle[$i] = $this->circle[$i - 1];
//        }
//        $this->circle[$new_marble_position] = $this->getCurrentMarble();

        $this->current_position = $new_marble_position;
    }

    private function addPointsToCurrentPlayer(int $points): void
    {
        if (!isset($this->scores[$this->getPlayer()])) {
            $this->scores[$this->getPlayer()] = 0;
        }
        $this->scores[$this->getPlayer()] += $points;
    }
}
