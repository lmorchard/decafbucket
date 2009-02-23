    <li class="entry">
        <h3 class="title"><a href="entries/<?= date('Y/m/d', $entry['date']) ?>"><?= date('D, d M Y', $entry['date']) ?></a></h3>
        <div class="body"><?= $entry['html'] ?></div>
    </li>
