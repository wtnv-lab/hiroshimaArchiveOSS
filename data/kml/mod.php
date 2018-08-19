<?php

$fileName = "jiji";
$filePath = $fileName . ".kml";
$xml = simplexml_load_file($filePath);

$jsonArray = array();

$documentArray = array(
  "id"=>"document",
  "name"=>$fileName,
  "version"=>"1.0",
  );

array_push($jsonArray, $documentArray);

$id = 1;

foreach ($xml->Document->Placemark as $placemark) {

  $color = (string)($placemark->styleUrl);

  $iconPath = '../icon/jiji/circle.png';

  $billboardName = (string)($placemark->name);
  $description_balloon = (string)($placemark->description);

  $point = explode(',',$placemark->Point->coordinates);

  foreach ($point as &$value){
    $value = (float)$value;
  }
  unset($value);

  $eyeOffset = array(
    "cartesian" => array(
      0,0,-200
      ) 
    );

  if ($color == "#Style2-point-1-map"){
    $rgba = array(
    // Red
      255,0,0,180
      );
  } elseif ($color == "#Style2-point-2-map") {
    // Yellow
    $rgba = array(
      233,61,40,200
      );
  } elseif ($color == "#Style2-point-3-map") {
    // Green
    $rgba = array(
      245,149,63,200
      );
  } elseif ($color == "#Style2-point-4-map") {
    // Blue
    $rgba = array(
      253,209,78,200
      );
  } elseif ($color == "#Style2-point-5-map") {
    // Purple
    $rgba = array(
      255,230,157,200
      );
  }

  $billboard = array(
    "horizontalOrigin" => "CENTER",
    "image" => $iconPath,
    "scale" => 0.5,
    "show" => "true",
    "color" => array(
      "rgba" => $rgba,  
      ),
    );

  $height = 50;

  $position = array(
    "cartographicDegrees" => array(
      $point[0],
      $point[1],
      $height
      )
    );

  $placemarkArray = array(
    "id" => "jiji" . $id,
    "name" => $billboardName,
    "description" => $description_balloon,
    "billboard" => $billboard,
    "position" => $position
    );

  if (empty($placemark->Point->coordinates)){
  } else {
    array_push($jsonArray, $placemarkArray);
  }
    $id++;
}

$json = json_encode($jsonArray,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
var_dump ($json);

file_put_contents($fileName . '.czml', $json);
?>