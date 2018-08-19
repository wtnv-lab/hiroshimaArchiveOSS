<?php

date_default_timezone_set('Asia/Tokyo');
$currentDate = date('c', time());

$db = mysql_connect("localhost", "phpmyadmin", "3nZXPiFC4pNhscv4");
//UTF-8を使う
mysql_query('SET NAMES utf8',$db);
if(!$db){ exit('MySQLに接続できません．');} else {
	//echo "MySQLに接続しました<br />";
}
if(!mysql_select_db("hiroshimaNagasaki2016")){ exit('データベースを選択できません．');} else {
	//echo "hiroshimaNagasaki2016に接続しました<br />";
}

$query = "SELECT DISTINCT date,name,message,country,longitude,latitude,target_id,target_la,target_lo FROM message";
$result = mysql_query($query);
//ヘッダでUTF-8指定
header('Content-type: text/plain; charset=UTF-8');

if(!$result){exit('クエリの実行が失敗しました: ');}

$jsonArray = array();

$documentArray = array(
    "id"=>"document",
    "name"=>"lineJson",
    "version"=>"1.0",
);

array_push($jsonArray, $documentArray);

$lineId = 0;
while($row = mysql_fetch_array($result)){
	$lineId = $lineId;

	$description = '<div class="lineMessage">' . $row['message'] . '</div>';
	$date = $row['date'];
	//if ($date == "2015-11-10 13:52:55") {
	//	$date = "2011-01-01 00:00:00";
	//}

	$dateOnly = substr($date,0,10);
	$timeOnly = substr($date,11);
	$date = (string)($dateOnly . "T" . $timeOnly . "+09:00");
	$availability = (string)($date . "/" . $currentDate);
	
	if ($row['longitude'] < 0) {
		$longitude = 180 - $row['longitude'];
	} else {
		$longitude = (float)$row['longitude'];		
	};

	$longitude = (float)$row['longitude'];
	$latitude = (float)$row['latitude'];
	$target_lo = (float)$row['target_lo'];	
	$target_la = (float)$row['target_la'];

/*	
	$dLon = $longitude - $row['target_lo'];
	$dLat = $row['latitude'] - $row['target_la'];
	
	$lonRad = deg2rad($dLon);
	$latRad = deg2rad($dLat);
	
	$distanceNS =  6400 * $latRad;
	$distanceWE =  cos(deg2rad($row['target_la'])) * 6400 * $lonRad;
	$distance = sqrt(pow($distanceWE,2) + pow($distanceNS,2));

	$points = array();
	$count = 0;	
	while ($count < 3){
		$newLon = $longitude - 0.5 * $dLon * $count;
		$newLat = $row['latitude'] - 0.5 * $dLat * $count;
		if ($count == 1){
				$point = array($newLon,$newLat,50 * $distance);
			} else {
				$point = array($newLon,$newLat,0);	
			}
		array_push($points, $point);
		$count++;
	}
*/

	if ($row['target_id'] == 'default'){
		$polylinePoint = array(
			132.4547065677773,
			34.39478216914307,
			0,
			$longitude,
			$latitude,
			0,
			129.8630754,
			32.7745281,
			0
		);		
	} else {
		$polylinePoint = array(
			$longitude,
			$latitude,
			0,
			$target_lo,
			$target_la,
			0
		);
	}

	$polylinePosition = array(
		"cartographicDegrees" => $polylinePoint,
	);

	$polylineRgba = array(
		3,221,255,48
	);

	$polylineColor = array(
		"rgba" => $polylineRgba,
	);

	$polylineSolidColor = array(
		"color" => $polylineColor,
	);

	$polylineGlowRgba = array(
		0,0,255,255
	);

	$polylineGlowColor = array(
		"rgba" => $polylineGlowRgba,
	);

	$polylineGlow = array(
		"color" => $polylineGlowColor,
		"glowPower" => 1.0,
	);

	$polyLineMaterial = array(
		"solidColor" => $polylineSolidColor,
//		"polylineGlow" => $polylineGlow,
	);

	$polyline = array(
		"width" => 1.75,
		"positions" => $polylinePosition,
		"material" => $polyLineMaterial,
		"positions" => $polylinePosition,
	);

	$placemarkArray = array(
		"id" => $lineId,
		"name" => "From " . $row['name'],
		"followSurface" => true,
//		"availability" => $availability,
//		"name" => $billboardName,
		"description" => $description,
//		"billboard" => $billboard,
//		"position" => $position,
		"polyline" => $polyline,
	);
	array_push($jsonArray, $placemarkArray);
	$lineId++;
}
//var_dump ($jsonArray);
$json = json_encode($jsonArray,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
if(json_last_error() !== JSON_ERROR_NONE){
    // エラーが発生
    print json_last_error_msg(); // エラーメッセージを出力
}
var_dump ($json);
file_put_contents('line.czml', $json);
?>