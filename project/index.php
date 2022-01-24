<?php declare(strict_types=1);

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
