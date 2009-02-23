<?php
/**
 *
 */
class Entries_Controller extends Controller {

    protected $auto_render = TRUE;

    public function __construct()
    {
        parent::__construct();

        $this->entries_model = new Entries_Model();
    }

	public function index()
    {
        $start   = isset($_GET['start']) ? $_GET['start'] : 0;
        $count   = isset($_GET['count']) ? $_GET['count'] : 7;
        $total   = $this->entries_model->getEntryCount();
        $entries = $this->entries_model->getEntries($start, $count);

        $prev_start = (($start - $count) >= 0) ? $start - $count : null;
        $next_start = (($start + $count) <= $total) ? $start + $count : null;

        $this->setViewData(array(
            'start'      => $start,
            'count'      => $count,
            'total'      => $total,
            'next_start' => $next_start,
            'prev_start' => $prev_start,
            'entries'    => $entries
        ));

        switch (Router::$current_uri) {
            case 'index.rss':
                $_GET['feed'] = 'rss'; break;
            case 'index.atom':
                $_GET['feed'] = 'atom'; break;
        }

        if (isset($_GET['feed'])) {
            $this->layout = '';
            switch ($_GET['feed']) {
                case 'rss':
                    $this->view = 'entries/feed_rss'; break;
                case 'atom':
                default:
                    $this->view = 'entries/feed_atom'; break;
            }
        }

	}

    public function single($yr, $mo, $da)
    {
        $date = "{$yr}/{$mo}/{$da}";
        $entry = $this->entries_model->getEntry($date);
        if (empty($entry)) {
            return Event::run('system.404');
        }
        $next_date = $this->entries_model->findNextDate($date);
        $prev_date = $this->entries_model->findPreviousDate($date);
        $this->setViewData(array(
            'entry'     => $entry,
            'next_date' => $next_date,
            'prev_date' => $prev_date
        ));
    }

}
