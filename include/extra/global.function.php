<?php
function time_elapsed($ptime)
{
    $etime = time() - strtotime($ptime);

    if ($etime < 1)
    {
        $array['value'] = 0;
        $array['prefix'] = "DATE_SECONDS";
        return $array;
    }

    $a = array( 365 * 24 * 60 * 60  =>  'DATE_YEAR',
                 30 * 24 * 60 * 60  =>  'DATE_MONTH',
                      24 * 60 * 60  =>  'DATE_DAY',
                           60 * 60  =>  'DATE_HOUR',
                                60  =>  'DATE_MINUTE',
                                 1  =>  'DATE_SECOND'
                );
    $a_plural = array( 'DATE_YEAR'   => 'DATE_YEARS',
                       'DATE_MONTH'  => 'DATE_MONTHS',
                       'DATE_DAY'    => 'DATE_DAYS',
                       'DATE_HOUR'   => 'DATE_HOURS',
                       'DATE_MINUTE' => 'DATE_MINUTES',
                       'DATE_SECOND' => 'DATE_SECONDS'
                );

    foreach ($a as $secs => $str)
    {
        $d = $etime / $secs;
        if ($d >= 1)
        {
            $r = round($d);
            $array['value'] = $r;
            $array['prefix'] = ($r > 1 ? $a_plural[$str] : $str);
            return $array;
        }
    }
}