<?php
$site_base = ( empty($_SERVER['HTTPS']) ? 'http://' : 'https://' ) . 
    $_SERVER['HTTP_HOST'] . url::base();

$x = new Memex_XmlWriter(array(
    'parents' => array( 'rss', 'channel', 'item' )
));

$x->rss(array(
        'version'    => '2.0',
        'xmlns:atom' => 'http://www.w3.org/2005/Atom'
    ))
    ->channel()
        ->title(Kohana::config('Config.site_title'))
        ->description(Kohana::config('Config.site_subtitle'))
        ->pubDate(date('r', $entries[0]['date'] + ( 60 * 60 * 24 - 60 )))
        ->link($site_base)
        ->element('atom:link', array(
            'rel'  => 'self',
            'type' =>'application/atom+xml', 
            'href' => $site_base . url::current(TRUE) 
        ))
        ->managingEditor(
            Kohana::config('Config.site_author_name') .
            '<'.Kohana::config('Config.site_author_email').'>'
        );

foreach ($entries as $entry) {

    $url = $site_base . 'entries/' . date('Y/m/d', $entry['date']);

    $x->item()
        ->title(date('Y/m/d', $entry['date']))
        ->link($url)
        ->guid($url)
        ->pubDate(date('r', $entry['date'] + ( 60 * 60 * 24 - 60 )))
        ->description($entry['html']);
    
    $x->pop();
}

$x->pop();
$x->pop();

echo $x->getXML();
