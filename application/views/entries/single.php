<?php slot::set('page_title', date('Y/m/d', $entry['date'])) ?>

<ul class="pagination">
    <?php if ($prev_date !== null): ?>
        <li class="prev"><a href="<?= url::base() . 'entries/' . $prev_date ?>"><?= $prev_date ?></a></li>
    <?php endif ?>
    <?php if ($next_date !== null): ?>
        <li class="next"><a href="<?= url::base() . 'entries/' . $next_date ?>"><?= $next_date ?></a></li>
    <?php endif ?>
</ul>
<ul id="entries">
    <?php View::factory('entries/entry', array(
        'entry' => $entry, 'show_thread' => true
    ))->render(true) ?>
</ul>
