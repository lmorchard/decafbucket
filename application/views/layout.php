<html>
    <head>
        <meta http-equiv="content-type" content="text/html;charset=utf-8">
        <link rel="stylesheet" type="text/css" href="<?= url::base() ?>css/bucket.css" />
        <script type="text/javascript" src="<?= url::base() ?>js/mootools-1.2.1-core-yc.js"></script>
        <script type="text/javascript" src="<?= url::base() ?>js/mootools-1.2-more.js"></script>
        <script type="text/javascript" src="<?= url::base() ?>js/bucket.js"></script>
        <link rel="alternate" type="application/atom+xml" title="RSS" href="<?= url::base() ?>index.atom" />
        <title><?= slot::output('page_title') ?><?php if (slot::exists('page_title')): ?> - <?php endif ?><?= out::H(Kohana::config('config.site_title')) ?></title>
    </head>
    <body class="theme-bucket">
        <div id="wrapper">
            <div id="header">
            <a href="<?= url::base() ?>"><h1 class="title"><?= out::H(Kohana::config('config.site_title')) ?></h1></a>
            </div>
            <div id="content">
                <?php echo $content ?>
            </div>
            <div id="footer">
                Source code for this bucket available at <a href="http://github.com/lmorchard/decafbucket/tree/master">GitHub</a>. Share and Enjoy.
            </div>
        </div>
        <script type="text/javascript" src="http://disqus.com/forums/<?= Kohana::config('config.disqus_shortname') ?>/embed.js"></script>
        <script type="text/javascript">
        //<![CDATA[
        (function() {
                var links = document.getElementsByTagName('a');
                var query = '?';
                for(var i = 0; i < links.length; i++) {
                    if(links[i].href.indexOf('#disqus_thread') >= 0) {
                        query += 'url' + i + '=' + encodeURIComponent(links[i].href) + '&';
                    }
                }
                document.write('<script charset="utf-8" type="text/javascript" src="http://disqus.com/forums/<?= Kohana::config('config.disqus_shortname') ?>/get_num_replies.js' + query + '"></' + 'script>');
            })();
        //]]>
        </script>

    </body>
</html>
