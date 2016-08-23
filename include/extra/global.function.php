<?php

function cmp($a, $b)
{
    return strcmp($a->date_assigned, $b->date_assigned);
}

function month_num_to_string($month_num = 0) {
    if(!is_numeric($month_num) || !is_int((int)$month_num)) {
        return;
    }
    
    if($month_num > 12 || $month_num < -11) {
        return;
    }
    
    if($month_num < 1) {
        $month_num = 12 - $month_num;
    }
    
    return date('F', mktime(0, 0, 0, $month_num, 10));
}

function day_num_to_string($day_num = 0) {
    if(!is_numeric($day_num) || !is_int((int)$day_num)) {
        return;
    }

    if($day_num > 6)
    {
        $day_num = $day_num % 7;
    }

    if($day_num < 0) {
        $day_num = 7 + $day_num;
    }

    return jddayofweek($day_num-1,2);
}

function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
    $sort_col = array();
    foreach ($arr as $key=> $row) {
        $sort_col[$key] = $row[$col];
    }
    array_multisort($sort_col, $dir, $arr);
}

function array_value_exists_in_key($array, $key, $val) {
    foreach ($array as $item) {
        if (isset($item[$key]) && $item[$key] == $val) {
            return true;
        }
    }
    return false;
}

function array_group_by_key(array $arr) {
    $result = array();
    foreach ($arr as $i) {
      $key = key($i);
      $result[$key][] = $i;
    }  
    return $result;
}

function test_dump($arr){
    echo "<pre>";
    var_dump($arr);
    echo "</pre>";
}

function object_group_by_key($ob, $k) {
    $result = array();
    foreach ($ob as $i) {
      $result[$i->$k][] = $i;
    }  
    return $result;
}

function generate_in_query($array) {
    $in_array = "";
    for($i = 0; $i < count($array); $i++) {
        $in_array .= $i > 0 ? ", '" . $array[$i] ."'" : "'" . $array[$i] ."'";
    }
    return $in_array;
}

function get_progress_color($progress = 0) {
    $progress = empty($progress) || !is_numeric($progress) ? 0 : $progress;
    
    switch($progress) {
        case $progress == 100:
            return "#36ce1c";
            
        case $progress > 70:
            return "#e8e323";
            
        case $progress > 40:
            return "#f3c02c";
            
        default:
            return "#f15530";
    }
}

function merge_array_recursively($array1, $array2, $overwrite = true) 
{ 
    foreach($array2 as $key=>$val) 
    { 
        if(isset($array1[$key])) 
        { 
            if(is_array($val)) 
                $array1[$key] = merge_array_recursively($array1[$key], $val); 
            elseif((is_string($array1[$key]) || is_int($array1[$key])) && $overwrite) 
                $array1[$key] = $val; 
        } 
        else 
            $array1[$key] = $val; 
    } 
    return $array1; 
} 

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