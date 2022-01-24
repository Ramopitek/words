<?php declare(strict_types=1);

class WordsGame
{
    private int $subjectId;
    private array $sessHistory;

    public function __construct($subjectId)
    {
        $this->subjectId = (int)$subjectId;
        $this->sessHistory = $_SESSION['words'] ? json_decode($_SESSION['words'], true) : [];
    }

    public function say(string $word, bool $sure = false): array
    {
        $previousWord = $this->getLastHistory();
        if (!$word) {
            return ['success' => false];
        }
        $lastLetter = $this->getLastLetter($previousWord);
        $firstLetter = mb_substr($word, 0, 1);

        if (mb_strtolower($lastLetter) !== mb_strtolower($firstLetter)) {
            return ['success' => false, 'wrong' => $lastLetter];
        }

        $wordArr = DB::queryFirstRow("SELECT * FROM words WHERE subjectId = {$this->subjectId} AND `text` = '$word'");
        if (!$wordArr) {
            if (!$sure) {
                return ['success' => false, 'warning' => 'Вы уверены что слово "' . $word . '" существует?'];
            } else {
                $newWordId = (int)$this->addNewWord($word);
                $this->addSessHistory($newWordId);
                return ['success' => true];
            }
        } else {
            return $this->checkWordHistory((int)$wordArr['id']);
        }
    }

    private function getLastLetter(string $word): string
    {
        $lastLetter = mb_substr($word, -1);
        if (in_array($lastLetter, ['ь', 'ъ', '.'])) {
            return mb_substr($word, -2, 1);
        } else {
            return $lastLetter;
        }
    }

    public function hear(): array
    {
        $whereHistory = "";
        if (sizeof($this->sessHistory) > 0) {
            $historyStr = implode(",", $this->sessHistory);
            $previousWord = $this->getLastHistory();
            $lastLetter = $this->getLastLetter($previousWord);
            $whereHistory = "AND id not in ($historyStr) AND LOWER( `text` ) LIKE '$lastLetter%'";
        }
        $sql = "SELECT * 
                FROM words 
                WHERE subjectId = {$this->subjectId}
                $whereHistory
                ORDER BY RAND()";
        $word = DB::queryFirstRow($sql);
        if (!$word) {
            return ['error' => 'Ты выйграл=('];
        } else {
            $this->addSessHistory((int)$word['id']);
            return ['success' => $word['text']];
        }
    }

    private function getHistoryIds()
    {
        return array_reverse($this->sessHistory);
    }

    public function getHistory(): array
    {
        if (!sizeof($this->sessHistory)) {
            return [];
        }
        $idsStr = implode(',', $this->getHistoryIds());
        $whereAndOrder = " AND id in ($idsStr) ORDER BY field (id, $idsStr)";
        $sql = "SELECT text FROM words WHERE subjectId = {$this->subjectId} $whereAndOrder";
        return DB::queryOneColumn("text", $sql);
    }

    public function getLastHistory()
    {
        return $this->getHistory()[0];
    }

    private function addNewWord(string $word)
    {
        $inserted = DB::insert("words", ['text' => $word, 'subjectId' => $this->subjectId]);
        if ($inserted) {
            return DB::queryFirstRow("SELECT * FROM words WHERE subjectId = {$this->subjectId} AND `text` = '$word'")['id'];
        } else {
            return false;
        }
    }

    private function addSessHistory(int $wordId)
    {
        $this->sessHistory[] = $wordId;
        $_SESSION['words'] = json_encode($this->sessHistory);
    }

    private function checkWordHistory(int $wordId): array
    {
        $inHistory = in_array($wordId, $this->sessHistory);
        if ($inHistory) {
            return ['success' => false, 'error' => "Такое слово уже было!"];
        } else {
            $this->addSessHistory((int)$wordId);
            return ['success' => true];
        }
    }

}