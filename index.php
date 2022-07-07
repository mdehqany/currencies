<?php
function load($url,$data = null){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
    if($data != null){
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
    }
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$server_output = curl_exec($ch);
    if(curl_error($ch)){echo curl_error($ch); }
	curl_close($ch);

	return $server_output;
}

//Result will be in $res variable
$res = array();
//First Of all Let load website content
$codes = load("https://fxmarketrate.cbi.ir/");
//Step One I want get date of today
preg_match("#<span id=\"MainContent_ViewCashChequeRates_spnDate\">(.*)<\/span>#Us",$codes,$date);
//CHeck if cant get date use global date Function of php
$res['today'] = isset($date[1]) ? $date[1] : date("Y-m-d");
//lets get tbody s for get prices
preg_match_all("#<tbody>(.*)<\/tbody>#Us",$codes,$tbls);
//Our table is in $tbls[1][1]; lets get all rows
if(isset($tbls[1]) && isset($tbls[1][1])){
    preg_match_all("#<tr>(.*)<\/tr>#Us",$tbls[1][1],$rows);
    //Ok now we have all rows in $rows[1];
    if(isset($rows[1]) ){
        foreach($rows[1] as $row){
            //Lets split every column
            preg_match_all("#<td>(.*)<\/td>#Us",$row,$col);
            $res['currencies'][ltrim(rtrim($col[1][1]))] = array("name"=>$col[1][0],"buy"=>preg_replace("#[^0-9]#","",$col[1][2]),"sell"=>preg_replace("#[^0-9]#","",$col[1][3]));
        }
    }
}
echo json_encode($res);
?>