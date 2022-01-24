<?php declare(strict_types=1);
/*
 * Составить программу, позволяющую компьютеру и человеку играть в слова.
 * Предварительно программа объясняет правила игры и позволяет уточнить их в любой момент.
 * Тематикой игры могут быть по выбору города, животные, растения и т.д.
 * Тематику из предложенных компьютером (не менее 3) выбирает человек.
 * Для игры компьютер использует собственную базу данных (для каждой тематики свою).
 * Если названное человеком слово отсутствует в базе, уточняется, правильно ли оно названо,
 * и в случае правильности заносится в базу.
 * Правила игры: первый игрок называет слово, а второй должен предложить другое,
 * начинающееся с той буквы, на которую оканчивается названное.
 */
header('Content-Type: text/html; charset=utf-8');
session_start();

require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/dbconf.php";
require_once __DIR__ . "/WordsGame.php";

include_once __DIR__ . "/html/head.html";
echo "HELLO WORD!";
if (!empty($_REQUEST['reset']) || !empty($_REQUEST['play'])) {
    unset($_SESSION['words']);
    unset($_SESSION['subject']);
}

$subjectId = $_REQUEST['subject'] ?? $_SESSION['subject'];

//var_dump(['subid' => $subjectId]);
//var_dump(['session' => $_SESSION]);
//var_dump(['request' => $_REQUEST]);

if (!empty($_REQUEST['play']) || !empty($_REQUEST['say']) || !(empty($_REQUEST['sureSay']))) {
    if (!empty($subjectId)) {
        include_once __DIR__ . "/html/game_form.php";
    } else {
        $error = "Тематика не выбрана.";
        include_once __DIR__ . "/html/subject_form.php";
    }
} else {
    include_once __DIR__ . "/html/subject_form.php";
}

include_once __DIR__ . "/html/foot.html";
