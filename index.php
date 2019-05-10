<?php 
error_reporting(0);

include 'img.processor.php';

// Source Path Where All Images Should Be Located After Processed
$baseImagePath = "img/";
// Source Path Where All Images Located
$baseSourcePath = "img/source";
// Rights For New Folders
$rights = 0755;
// CompressionQuality
$compressionQuality = 50;

// Thumbnail CompressionQuality
$thumbnailCompressionQuality = 25;
$thumbnailPrefix = "thumb_";

// Speichern der Bilder
$sizesList = ["215", "430", "860", "1920"];
$excludeList = ["bg"];

/* DO NOT EDIT BELOW */

// Start Routine
$imageList = bootstrapApplication($baseSourcePath, $baseImagePath, $sizesList, $rights, $excludeList);
// Rescale + Progressive 
processImages($imageList, $sizesList, $baseImagePath, $baseSourcePath, $compressionQuality, $thumbnailCompressionQuality, $thumbnailPrefix);


?>