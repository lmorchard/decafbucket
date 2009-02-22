<?php
// Construct the site absolute base URL.
$site_base = ( empty($_SERVER['HTTPS']) ? 'http://' : 'https://' ) . 
    $_SERVER['HTTP_HOST'] . url::base();

$x = new Memex_XmlWriter(array(
    'parents' => array( 'feed', 'entry', 'author' )
));

$x->feed(array('xmlns'=>'http://www.w3.org/2005/Atom'))
    ->id($site_base . url::current(TRUE))
    ->title(Kohana::config('Config.site_title'))
    ->updated(date('c', $entries[0]['date'] + ( 60 * 60 * 24 - 60 )))
    ->link(array(
        'rel'  => 'alternate', 
        'type' => 'text/html', 
        'href' => $site_base
    ))
    ->author()
        ->name(Kohana::config('Config.site_author_name'))
        ->email(Kohana::config('Config.site_author_name'))
        ->url(Kohana::config('Config.site_author_url'))
    ->pop()
    ;

// Add a self-reference link.
$x->link(array(
    'rel'  => 'self',
    'type' =>'application/atom+xml', 
    'href' => $site_base . url::current(TRUE) 
));

// Add a pagination links. (RFC 5005)
$links = array(
    'first'    => 0,
    'previous' => $prev_start,
    'next'     => $next_start,
    'last'     => $total
);
foreach ($links as $name=>$link_start) {
    if ($link_start === null) continue;
    $x->link(array(
        'rel'  => $name,
        'type' => 'application/atom+xml', 
        'href' => $site_base . '?' . http_build_query(array(
            'feed' => 'atom', 'count' => $count, 'start' => $link_start
        ))
    ));
}

// Now, finally, add all the posts as feed entries.
foreach ($entries as $entry) {

    $url = $site_base . 'entries/' . date('Y/m/d', $entry['date']);

    $x->entry()
        ->title(date('Y/m/d', $entry['date']))
        ->link(array( 'href' => $url ))
        ->id($url)
        ->updated(date('c', $entry['date']))
        ->published(date('c', $entry['date'] + ( 60 * 60 * 24 - 60 )))
        // ->summary(join("\n\n", array_values($entry['raw'])))
        ->content(array('type' => 'html'), $entry['html']);
    
    /*
    if (!empty($post['notes'])) {
        $x->summary(array('type'=>'text'), $post['notes']);
    }
    */

    $x->pop();
}

$x->pop();

echo $x->getXML();
