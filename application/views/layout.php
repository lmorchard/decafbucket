<html>
    <head>
        <meta http-equiv="content-type" content="text/html;charset=utf-8">
        <link rel="stylesheet" type="text/css" href="<?= url::base() ?>css/bucket.css" />
        <script type="text/javascript" src="http://www.haloscan.com/load/lmorchard"> </script>
        <script type="text/javascript" src="<?= url::base() ?>js/mootools-1.2.1-core-yc.js"></script>
        <script type="text/javascript" src="<?= url::base() ?>js/mootools-1.2-more.js"></script>
        <script type="text/javascript" src="<?= url::base() ?>js/bucket.js"></script>
        <link rel="alternate" type="application/atom+xml" title="RSS" href="<?= url::base() ?>index.atom" />
        <title><?= out::H(Kohana::config('Config.site_title')) ?></title>
    </head>
    <body class="theme-bucket">
        <div id="wrapper">
            <div id="header">
            <a href="<?= url::base() ?>"><h1 class="title"><?= out::H(Kohana::config('Config.site_title')) ?></h1></a>
            </div>
            <div id="content">
                <?php echo $content ?>
            </div>
            <div id="footer">
            </div>
        </div>
    </body>
</html>
