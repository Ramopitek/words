<?php declare(strict_types=1);

$game = new WordsGame($subjectId);
$sure = $_REQUEST['sureSay'] ?? false;
if ($_REQUEST['say'] || $sure) {
    $say = $game->say($_REQUEST['myword'] ?? $sure, !!$sure);
}

if (!$_REQUEST['say'] || $say['success'] == true) {
    $hear = $game->hear();
}

if ($hear['success'] || $say['success'] == false) {
    ?>
    <form action="/" method="POST">
        <input type="hidden" name="subject" value="<?= $subjectId ?>"
        <div>
            Я говорю: <?= $hear['success'] ?? $game->getLastHistory() ?>
        </div>
        <div>
            <? if ($say['error']) { ?>
                <div> Слово "<?= $_REQUEST['myword'] ?>" уже было!</div>
            <? } ?>
            <? if ($say['warning']) { ?>
                <div><?= $say['warning'] ?></div>
                <button name="sureSay" value="<?= $_REQUEST['myword'] ?>">Уверен</button>
                <button name="say" value="1">Скажу другое слово</button>
            <? } else { ?>
                <?=$say['wrong']?"Слово должно начинаться на {$say['wrong']}":""?>
                <input type="text" autofocus autocomplete="off" name="myword"/>
                <button name="say" value="1">Сказать</button>
            <? } ?>
        </div>
    </form>
    <br>
<? } elseif ($hear['error']) { ?>
    <div><?= $hear['error'] ?></div>
<? } ?>
    <form action="/" method="POST">
        <button name="reset" value="1">Заного</button>
    </form>
<?
if ($history = $game->getHistory()) {
    ?>
    <div>
        <label>История:</label>
        <ul>
            <?
            foreach ($history as $h) { ?>
                <li><?= $h ?></li>
            <? } ?>
        </ul>
    </div>
<? } ?>