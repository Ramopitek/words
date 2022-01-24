<?php declare(strict_types=1);

$subjects = DB::query("SELECT * FROM subjects");
?>
<form action="/" method="POST">
    <?
        if (isset($error)) { ?>
            <b><?=$error?></b>
        <? }
    ?>
    <div>
        Выбери тему:
    </div>
    <div>
        <?
        foreach ($subjects as $s) { ?>
            <label>
                <input name="subject" type="radio" value="<?= $s['id'] ?>"/>
                <? echo $s['name'] ?>
            </label>
        <? }
        ?>
    </div>
    <br>
    <div>
        <button value="1" name="play">Играть</button>
    </div>
</form>
