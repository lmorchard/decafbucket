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
        'textile'  => 'formatTextile',
        'html'     => 'formatPassthrough'
    );

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->path = Kohana::config('model.entries_path');
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
        $files = glob("{$this->path}/{$yr}/{$mo}/*", GLOB_ONLYDIR);
        $das = array_map(array($this, 'extractDayFromPath'), $files);
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
        $dirs = glob($this->path . '/*/*/*', GLOB_ONLYDIR);
        $dates = array_map(array($this, 'removePath'), $dirs);
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
            array($this, 'removePath'),
            glob("{$this->path}/{$yr}/{$mo}/{$da}")
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
     * Get a count of available entries
     *
     * @param numeric count of entries
     */
    public function getEntryCount()
    {
        $exts = "{" . join(",", array_keys($this->formatter_map)) . "}";
        $files = glob("{$this->path}/*/*/*/*.{$exts}", GLOB_BRACE);
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

        $exts = "{" . join(",", array_keys($this->formatter_map)) . "}";
        $files = glob("{$this->path}/{$date}/*.{$exts}", GLOB_BRACE);
        
        $raw = array();
        $html = array();
        foreach ($files as $fn) {
            if (realpath($fn) != $fn) continue;
            if (!is_readable($fn)) continue;

            $ext = pathinfo($fn, PATHINFO_EXTENSION);
            if (isset($this->formatter_map[$ext])) {
                $ct = $this->filterContent(file_get_contents($fn));
                $raw[$fn] = $ct;
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

    public function formatMarkdown($raw)
    {
        require_once 'Markdown.php';
        return Markdown($raw);
    }

    public function formatTextile($raw)
    {
        $t = new Textile();
        return $t->TextileThis($raw);
    }

    public function formatPassthrough($raw)
    {
        return $raw;
    }

    public function filterContent($raw)
    {
        $lines = array_filter( 
            explode("\n", $raw),
            array($this, 'filterLine')
        );
        return join("\n", $lines);
    }

    public function filterLine($line)
    {
        return (
            strpos($line, '/* ') !== 0 &&
            strpos($line, '// ') !== 0
        );
    }

    private function extractDayFromPath($fn)
    {
        $fn = str_replace($this->path.'/', '', $fn);
        $parts = explode('/', $fn);
        $name = array_pop($parts);
        return $name;
    }

    private function removePathAndYear($fn)
    {
        $fn = str_replace($this->path.'/', '', $fn);
        list($undef, $mo) = explode('/', $fn, 2);
        return $mo;
    }

    private function removePath($fn)
    {
        return str_replace($this->path.'/', '', $fn);
    }

}
