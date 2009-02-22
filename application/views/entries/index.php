<ul class="pagination">
    <?php if ($prev_start !== null): ?>
        <li class="prev"><a href="<?= url::base() . "?start={$prev_start}&count={$count}" ?>">prev page</a></li>
    <?php endif ?>
    <?php if ($next_start !== null): ?>
        <li class="next"><a href="<?= url::base() . "?start={$next_start}&count={$count}" ?>">next page</a></li>
    <?php endif ?>
</ul>
<ul id="entries">
    <?php foreach ($entries as $entry): ?>
        <?php View::factory('entries/entry', array(
            'entry' => $entry
        ))->render(true) ?>
    <?php endforeach ?>
</ul>
<ul class="pagination">
    <?php if ($prev_start !== null): ?>
        <li class="prev"><a href="<?= url::base() . "?start={$prev_start}&count={$count}" ?>">prev page</a></li>
    <?php endif ?>
    <?php if ($next_start !== null): ?>
        <li class="next"><a href="<?= url::base() . "?start={$next_start}&count={$count}" ?>">next page</a></li>
    <?php endif ?>
</ul>
