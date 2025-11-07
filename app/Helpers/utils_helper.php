<?php

if ( ! function_exists('imageExists'))
{
    function imageExists($url) {  
        // Suppress warnings and check if the file can be retrieved  
        $headers = @get_headers($url);  
        
        // Check if the headers were successfully retrieved  
        if ($headers && strpos($headers[0], '200') !== false) {  
            return true; // Image exists  
        }  
        
        return false; // Image does not exist  
    } 

    function text2html($text) {
        $html = str_replace("\n", "<br/>", $text);
        return $html;
    }

    function floatV($text) {
        $f = floatval(preg_replace('/[^0-9.]/', '', trim($text)));
        $f = str_replace(',', '', $f);
        $f = floatval($f);
        return $f;
    }

    function str_find($text, $pattern) {
        $delimiters = "/[,; ]/";
        $array = preg_split($delimiters, $text); 
        foreach($array as $a) {
            if(strtolower($a) == strtolower($pattern)) return true;
        }
        return false;
    }

    function checkDateRangeCross($sDate1, $eDate1, $sDate2, $eDate2) 
    {
        if($sDate2 < $sDate1 && $sDate1 < $eDate2 ) return true;
        if($sDate2 < $eDate1 && $eDate1 < $eDate2 ) return true;
        if($sDate1 < $sDate2 && $sDate2 < $eDate1 ) return true;
        if($sDate1 < $eDate2 && $eDate2 < $eDate1 ) return true;
        return false;
    }
}
?>