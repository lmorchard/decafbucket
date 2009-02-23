    <li class="entry">
        <h3 class="title"><a href="<?= url::base() ?>entries/<?= date('Y/m/d', $entry['date']) ?>"><?= date('D, d M Y', $entry['date']) ?></a></h3>
        <div class="body"><?= $entry['html'] ?></div>
        <span class="comments">
            <a class="commentlink" href="<?= url::base() ?>entries/<?= date('Y/m/d', $entry['date']) ?>#disqus_thread">Comments</a>
        </span>
        <?php if (isset($show_thread)): ?>
            <div id="disqus_thread"></div>
        <?php endif ?>
    </li>
