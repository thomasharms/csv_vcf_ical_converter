<?php
require_once('model/user.php');

require_once('model/session.php');
require_once('model/vcard_vonverter.php');




$string = file_get_contents("thomas harms.vcf");
preg_match_all('|(BEGIN:VCARD.*?END:VCARD)|sm', $string, $vCard_set);
$line_set = preg_split ('/$\R?^/m', $vCard_set[0][0]);
foreach ($line_set as $l){
    //print($l.'</br>');
    $att_vals = preg_split('|[:;]|s', $l);
    if(in_array(!empty(preg_match('\(EMAIL)\ms',$att_vals, $a)))
    /*foreach($att_vals as $s){
        if(empty($s)){
            print("1".'</br>');
        }else
        
        {print($s.'</br>');}
        
    }
    print('</br></br>');
}


/*
 *$file = "Judith Abraham und 302 weitere.vcf";
$a = new vCard_Converter($file);

$a->to_array();
 
foreach ($line_set as $l){
    $att_vals = preg_split('%(.*?)[:;]%', $l);
    print(count($att_vals));
}

preg_match_all('|(BEGIN:VCARD.*?END:VCARD)|sm', $string, $vCard_set);
print(count($vCard_set[0])."____".count($vCard_set[1]));
foreach($vCard_set as $vcard){
    
    print("start new vcard!!!!!</br>");
    foreach($vcard as $e){
        print($e."</br>");
    }
    print("end new vcard!!!!!</br>");
}


function get_charset($string)
	{
		if (substr($string, 0, 4) == "\0\0\xFE\xFF") return 'UTF-32BE';  // Big Endian
		if (substr($string, 0, 4) == "\xFF\xFE\0\0") return 'UTF-32LE';  // Little Endian
		if (substr($string, 0, 2) == "\xFE\xFF") return 'UTF-16BE';      // Big Endian
		if (substr($string, 0, 2) == "\xFF\xFE") return 'UTF-16LE';      // Little Endian
		if (substr($string, 0, 3) == "\xEF\xBB\xBF") return 'UTF-8';

		// no match, check for utf-8
		//if (is_utf8($string)) return 'UTF-8';

		// heuristics
		if ($string[0] == "\0" && $string[1] == "\0" && $string[2] == "\0" && $string[3] != "\0") return 'UTF-32BE';
		if ($string[0] != "\0" && $string[1] == "\0" && $string[2] == "\0" && $string[3] == "\0") return 'UTF-32LE';
		if ($string[0] == "\0" && $string[1] != "\0" && $string[2] == "\0" && $string[3] != "\0") return 'UTF-16BE';
		if ($string[0] != "\0" && $string[1] == "\0" && $string[2] != "\0" && $string[3] == "\0") return 'UTF-16LE';

		return false;
	}

function is_utf8($string)
	{
		return preg_match('/\A(
			[\x09\x0A\x0D\x20-\x7E]
			| [\xC2-\xDF][\x80-\xBF]
			| \xE0[\xA0-\xBF][\x80-\xBF]
			| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}
			| \xED[\x80-\x9F][\x80-\xBF]
			| \xF0[\x90-\xBF][\x80-\xBF]{2}
			| [\xF1-\xF3][\x80-\xBF]{3}
			| \xF4[\x80-\x8F][\x80-\xBF]{2}
			)*\z/xs', substr($string, 0, 2048));
	}
$string = file_get_contents("Judith Abraham und 302 weitere.vcf");
print($string[0]."\n");
print(substr($string, 0, 2048));
$result = get_charset($string);

if(!$result){
    print("2");
} else {print $result;}

*/
?>