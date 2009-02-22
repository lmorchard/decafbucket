    <li class="date_header">
        <h3><a href="entries/<?= date('Y/m/d', $entry['date']) ?>"><?= date('D, d M Y', $entry['date']) ?></a></h3>
    </li>
    <li class="entry">
        <div class="body"><?= $entry['html'] ?></div>
    </li>
