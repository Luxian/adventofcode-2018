<?php /** @noinspection AutoloadingIssuesInspection */

$start = microtime(true);

ini_set('memory_limit', '1G');
gc_disable(); // avoid segmentation fault caused by PHP bug

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
    echo 'Test #' . ($test_no + 1) . ": {$test['players']} players, last marble {$test['marble']}. => ";
    if ($game->getWinningScore() !== $test['winning_score']) {
        echo 'FAILED' . PHP_EOL;
        echo "Expected winning score {$test['winning_score']} and got " . $game->getWinningScore() . PHP_EOL;
        exit(1);
    }
    echo 'PASSED' . PHP_EOL;
}


$game = new MarbleGame(459, 72103 * 100);
$game->play();
echo 'Winning score is: ' . $game->getWinningScore() . PHP_EOL;
echo 'Winning elf is: ' . $game->getWinningElf() . PHP_EOL;

echo 'Duration: ' . number_format(microtime(true) - $start, 5) . ' seconds. ' .
    'Memory: ' . number_format(memory_get_peak_usage(true) / (1024 * 1024), 2) . ' MB.' .
    PHP_EOL;

class MarbleGame
{

    /** @var int */
    private $numberOfPlayers;

    /** @var int */
    private $lastMarble;

    /** @var  int[] */
    private $scores = [];

    /** @var int */
    private $current_player = 0;

    /** @var int */
    private $current_marble = 1;

    /** @var CircleItem */
    private $currentItem;

    public function __construct(int $number_of_players, int $last_marble)
    {
        $this->numberOfPlayers = $number_of_players;
        $this->lastMarble = $last_marble;

        $this->currentItem = new CircleItem(0);
        $this->currentItem->setNext($this->currentItem);
        $this->currentItem->setPrevious($this->currentItem);
    }

    public function play(): void
    {
        while(($next_marble = $this->getCurrentMarble()) <= $this->lastMarble) {
            echo 'Marble ' . $this->getCurrentMarble() . ' (' . round($this->getCurrentMarble() / $this->lastMarble * 100, 2) . "%)\r";
            if ($next_marble % 23) {
                $this->currentItem = $this->currentItem->getNext();
                $this->currentItem->insertAfter(new CircleItem($this->getCurrentMarble()));
                $this->currentItem = $this->currentItem->getNext();
            }
            else {
                $item_to_remove = $this->currentItem;
                for($i = 0; $i < 7; $i++) {
                    $item_to_remove = $item_to_remove->getPrevious();
                }

                $this->addPointsToCurrentPlayer($next_marble);
                $this->addPointsToCurrentPlayer($item_to_remove->getMarbleValue());

                $previous_item = $item_to_remove->getPrevious();
                $previous_item->setNext($item_to_remove->getNext());
                $item_to_remove->getNext()->setPrevious($previous_item);
                $this->currentItem = $item_to_remove->getNext();
                unset($previous_item);
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
        $this->current_player = ($this->current_player + 1) % $this->numberOfPlayers;
    }

    private function getCurrentMarble(): int
    {
        return $this->current_marble;
    }

    private function switchMarble(): void
    {
        $this->current_marble++;
    }

    private function addPointsToCurrentPlayer(int $points): void
    {
        if (!isset($this->scores[$this->getPlayer()])) {
            $this->scores[$this->getPlayer()] = 0;
        }
        $this->scores[$this->getPlayer()] += $points;
    }
}

class CircleItem {

    private $marbleValue;

    private $next;

    private $previous;

    public function __construct(int $value)
    {
        $this->marbleValue = $value;
    }

    public function setNext(CircleItem $next): void
    {
        $this->next = &$next;
    }

    public function setPrevious(CircleItem $previous): void
    {
        $this->previous = &$previous;
    }

    public function getMarbleValue(): int
    {
        return $this->marbleValue;
    }

    public function &getNext(): CircleItem
    {
        return $this->next;
    }

    public function &getPrevious(): CircleItem
    {
        return $this->previous;
    }

    public function insertAfter(CircleItem $new_item): void
    {
        $new_item->setNext($this->getNext());
        $new_item->setPrevious($this);

        $this->getNext()->setPrevious($new_item);
        $this->setNext($new_item);
    }
}
