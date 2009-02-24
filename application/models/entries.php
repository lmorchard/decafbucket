<?php
/**
 * Model providing access to entries.
 * Currently backed by the filesystem.
 *
 * @package    DecafBucket
 * @author     l.m.orchard@pobox.com
 */
class Entries_Model /* extends Model */ 
{
    protected $_content_cache = array();
    protected $_entry_cache = array();

    public $formatter_map = array(
        'txt'      => 'formatMarkdown',
        'md'       => 'formatMarkdown',
        'markdown' => 'formatMarkdown',
        'tx'       => 'formatTextile',
        'textile'  => 'formatTextile',
        'opml'     => 'formatOPML',
        'html'     => 'formatPassthrough'
    );

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->path = Kohana::config('model.entries_path');
        $this->exts = "{" . join(",", array_keys($this->formatter_map)) . "}";
    }

    /**
     * Get a list of years for entries.
     *
     * @return array list of years
     */
    public function getYears()
    {
        $dirs = glob($this->path . '/*', GLOB_ONLYDIR);
        $yrs = array_map(array($this, 'removePath'), $dirs);
        rsort($yrs);
        return $yrs;
    }

    /**
     * Get a list of months for a year of entries.
     *
     * @param string Year in which to look for months, defaults to current year.
     * @return array list of months
     */
    public function getMonthsForYear($yr=null)
    {
        if (!is_numeric($yr)) $yr = date('Y');
        $dirs = glob("{$this->path}/{$yr}/*", GLOB_ONLYDIR);
        $mos = array_map(array($this, 'removePathAndYear'), $dirs);
        rsort($mos);
        return $mos;
    }

    /**
     * For a directory to a month in a year, remove the path and year from the 
     * front.
     *
     * @param string absolute path to month directory
     * @return string month directory
     */
    private function removePathAndYear($fn)
    {
        $fn = str_replace($this->path.'/', '', $fn);
        list($undef, $mo) = explode('/', $fn, 2);
        return $mo;
    }

    /**
     * Get a list of days for a month in a year.
     *
     * @param string Year in which to look for months, defaults to current year.
     * @param string month in which to look for days, defaults to current month.
     * @return array list of days.
     */
    public function getDaysForMonthInYear($yr, $mo)
    {
        if (!is_numeric($yr)) $yr = date('Y');
        if (!is_numeric($mo)) $yr = date('m');
        $files = glob("{$this->path}/{$yr}/{$mo}/*");
        $das = array_map(array($this, 'convertPathIntoDate'), $files);
        rsort($das);
        return $das;
    }

    /**
     * List all known dates.
     *
     * @return array list of date paths.
     */
    public function getDates()
    {
        $dirs = glob($this->path . '/*/*/*');
        $dates = array_map(array($this, 'convertPathIntoDate'), $dirs);
        return $dates;
    }

    /**
     * Get the next date relative to a given date.
     *
     * @param string current date
     * @return string next date
     */
    public function findNextDate($date)
    {
        $dates = $this->getDates();

        $pos = array_search($date, $dates);
        if ($pos === FALSE) return null;
        if ($pos === 0) return null;
        return $dates[$pos - 1];
    }

    /**
     * Get the previous date relative to a given date.
     *
     * @param string current date
     * @return string previous date
     */
    public function findPreviousDate($date)
    {
        $dates = $this->getDates();

        $pos = array_search($date, $dates);
        if ($pos === FALSE) return null;
        if ($pos === count($dates) - 1) return null;
        return $dates[$pos + 1];
    }
    
    /**
     * Get a list of entries for a given year, month, and day.
     *
     * @param string year for search, defaults to all years if null
     * @param string month for search, defaults to all months if null
     * @param string day for search, defaults to all days if nul
     * @return array list of entry names
     */
    public function getEntriesByDate($yr=null, $mo=null, $da=null)
    {
        if (!is_numeric($yr)) $yr = '*';
        if (!is_numeric($mo)) $mo = '*';
        if (!is_numeric($da)) $da = '*';

        $dates = array_map(
            array($this, 'convertPathIntoDate'),
            array_merge(
                // HACK: Need to get both directories of entries, as well as 
                // single-day entry files.
                glob("{$this->path}/{$yr}/{$mo}/{$da}"),
                glob("{$this->path}/{$yr}/{$mo}/{$da}.{$this->exts}")
            )
        );
        rsort($dates);

        $entries = array();
        foreach ($dates as $date) {
            $entry = $this->getEntry($date);
            if (!empty($entry))
                $entries[] = $entry;
        }

        return $entries;
    }

    /**
     * Take an absolute path, either to a day's directory or a day's entry file 
     * and convert into a yyyy/mm/dd date.
     *
     * @param string absolute path
     * @return string a date in yyyy/mm/dd format
     */
    private function convertPathIntoDate($fn)
    {
        $fn = $this->removePath($fn);
        return substr($fn, 0, 10);
    }

    /**
     * Snip the current entries path from the front of an absolute path.
     *
     * @param string an absolute path
     * @return string the relative path
     */
    private function removePath($fn)
    {
        return str_replace($this->path.'/', '', $fn);
    }

    /**
     * Get a count of available entries
     *
     * @param numeric count of entries
     */
    public function getEntryCount()
    {
        $files = array_merge(
            // HACK: Need to get both directories of entries, as well as 
            // single-day entry files.
            glob("{$this->path}/*/*/*/*.{$this->exts}", GLOB_BRACE),
            glob("{$this->path}/*/*/*.{$this->exts}", GLOB_BRACE)
        );
        return count( $files );
    }

    /**
     * Return a number of recent entry names.
     *
     * @param number limit of entries to return
     * @param array list of names
     */
    public function getEntries($start=0, $count=10)
    {
        $entries = $this->getEntriesByDate();
        return array_slice($entries, $start, $count);
    }

    /**
     * Get an entry by date
     */
    public function getEntry($date)
    {
        if (isset($this->_entry_cache[$date]))
            return $this->_entry_cache[$date];

        $files = array_merge(
            // HACK: Need to get both directories of entries, as well as 
            // single-day entry files.
            glob("{$this->path}/{$date}/*.{$this->exts}", GLOB_BRACE),
            glob("{$this->path}/{$date}.{$this->exts}", GLOB_BRACE)
        );
        rsort($files);
        
        $raw = array();
        $html = array();
        foreach ($files as $fn) {
            if (realpath($fn) != $fn) continue;
            if (!is_readable($fn)) continue;

            $ext = pathinfo($fn, PATHINFO_EXTENSION);
            if (isset($this->formatter_map[$ext])) {
                
                $raw[$fn] = $ct = file_get_contents($fn);
                $html[] = call_user_func(
                    array($this, $this->formatter_map[$ext]), $ct
                ); 
            }
        }

        if (empty($raw)) return null;

        return $this->_entry_cache[$date] = 
            array(
                'id'    => $date,
                'path'  => $date,
                'date'  => strtotime($date),
                'html'  => join("\n\n", $html),
                'raw'   => $raw
            );
    }

    /**
     * Format raw text as HTML using Markdown
     *
     * @param string raw text
     * @param string formatted HTML
     */
    public function formatMarkdown($raw)
    {
        require_once 'Markdown.php';
        return Markdown($this->filterComments($raw));
    }

    /**
     * Format raw text as HTML using Textile
     *
     * @param string raw text
     * @param string formatted HTML
     */
    public function formatTextile($raw)
    {
        $t = new Textile();
        return $t->TextileThis($this->filterComments($raw));
    }

    /**
     * Format raw text as HTML using nothing
     *
     * @param string raw text
     * @param string formatted HTML
     */
    public function formatPassthrough($raw)
    {
        return $raw;
    }

    /**
     * Format raw OPML as HTML using an XSL stylesheet
     *
     * @param string raw OPML
     * @param string formatted HTML
     */
    public function formatOPML($raw)
    {
        $doc = simplexml_load_string($raw);
        return $this->_formatOutlineNode($doc->body);
    }

    /**
     * Format the outline children of a parsed OPML node.
     * Recursively called to format an entire outline.
     *
     * @param SimpleXMLElement parent outline node
     * @param text formatted HTML.
     */
    public function _formatOutlineNode($node)
    {
        if (empty($node)) return '';

        $out = array();
        $out[] = '<ul>';
        foreach ($node->outline as $outline) {
            $out[] = '<li>';
            if (!empty($outline['text'])) {
                $out[] = '<p>' . $outline['text'] . '</p>';
                // TODO: inject permalinks based on date/time or derived title.
            }
            if (count($outline->outline)) {
                $out[] = $this->_formatOutlineNode($outline);
            }
            $out[] = '</li>';
        }
        $out[] = '</ul>';
        return join("\n", $out);
    }

    /**
     * Filter comments from raw text, eg. lines starting with // and /*
     *
     * @param string raw text
     * @return string text filtered for comments
     */
    public function filterComments($raw)
    {
        $lines = array_filter( 
            explode("\n", $raw),
            array($this, 'filterCommentLine')
        );
        return join("\n", $lines);
    }

    /**
     * Determine whether a line contains a comment.
     *
     * @param string raw text line
     * @return boolean whether or not the line is a comment
     */    
    public function filterCommentLine($line)
    {
        return (
            strpos($line, '/* ') !== 0 &&
            strpos($line, '// ') !== 0
        );
    }

}
