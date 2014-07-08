<?php

class CSE
{
	public static function downloadDailySummaries()
    {
        $url = 'http://thecse.com/cmsAssets/docs/CNSX%20Trading%20Summaries/Daily/CNSXDailyMarketSummary.';
        $date = '2005-05-24'; // Oldest=2005-05-24
        $endDate = date('Y-m-d');

        while (strtotime($date) <= strtotime($endDate)) {
            $ch = curl_init($url . $date . '.txt');
            curl_setopt($ch,  CURLOPT_RETURNTRANSFER, TRUE);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode == 200)
                file_put_contents("cse/$date.txt", $response);

            curl_close($ch);
            $date = date ('Y-m-d', strtotime('+1 day', strtotime($date)));
        }
    }

    public static function parseFile($filePath, $date)
    {
        $f = file_get_contents($filePath);
        $lines = explode("\n", $f);

        $lines = array_slice($lines, 3);
        $table = [];

        $i = 0;
        while (isset($lines[$i]) && trim($lines[$i]) != NULL)
            $table[] = $lines[$i++];

        $movers = [];
        foreach ($table as $line)
        {
            $s = [];
            $line = self::explodeAccordingToDate($line, $date);
            $s['name'] = trim($line[0]);
            $s['symbol'] = trim($line[1]);
            $s['volume'] = trim($line[2]);
            $s['high'] = trim($line[3]);
            $s['low'] = trim($line[4]);
            $s['close'] = trim($line[5]);
            //$s['change'] = trim($line[6]);
            //$s['high52w'] = trim($line[7]);
            //$s['low52w'] = trim($line[8]);

            // Adjust volume to full value
            if (strpos($s['volume'], 'z') !== false)
                $s['volume'] = str_replace('z', '', $s['volume']);
            else if (strlen($s['volume']) > 0)
                $s['volume'] = $s['volume'] * 100;

            $movers[] = $s;
        }

        return $movers;
    }

    private static function explodeAccordingToDate($line, $date)
    {
        // File format changed on 2008-10-17 to always include a tab character between columns.
        // Before that, the first three columns had fixed lenght:
        // Company name (20), symbol (8), volume (15 or remainder)
        // The source files for the following dates are messed up so you need to
        // fix them by hand or take those provided:
        // 2007-06-20
        // 2007-06-21
        // 2008-10-23
        // 2009-11-18
        // 2010-04-20
        // 2010-04-28
        // 2011-02-24
        // 2011-04-04
        // 2011-04-08
        // 2011-05-02
        // 2011-07-06
        // 2012-08-30
        // 2013-09-30
        // 2013-12-04
        // 2014-02-05
        if ($date >= '2008-10-17')
        {
            return explode("\t", $line);
        }
        else
        {
            $tmp = explode("\t", $line);
            $a = substr($tmp[0], 0, 20);
            $b = substr($tmp[0], 20, 8);
            $c = substr($tmp[0], 28);
            array_shift($tmp);
            return array_merge([$a, $b, $c], $tmp);
        }
    }
}