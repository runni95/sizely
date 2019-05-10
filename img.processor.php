<?php
/* Function bootstrapApplication
@baseSourcePath = Base Path Where All Images Can Be Found
@baseImagePath = Base Path Where All Folders Can Be Found
@sizes = image sizes for creation
@rights = rights for folder ftp-rights
@excludeList = List of excluded filenames
*/
function bootstrapApplication($baseSourcePath, $baseImagePath, $sizes,  $rights, $excludeList){
	
	printMsgWithTag("Sourcepath Check", "h4");
	validatePath(false, null, $baseSourcePath);
	
	printMsgWithTag("Processpath Check", "h4");
	foreach ($sizes as $size){
		// Validate If All Required Folders ExistPrüfe ob entsprechende Unterverzeichnisse verfügbar sind
		// Create If Not
		validatePath(true, $rights, $baseImagePath.$size, true);
		// Last Element In Array?
		if( !next( $sizes ) ) {
			// Collect All Images And Set Result To $imageList
			$imageList = scanSourcePath($baseSourcePath, $excludeList);
			return $imageList;
		}
	}
}

/* Function processImages 
@images = array of images
@baseSourcePath = Base Path Where All Images Can Be Found
@baseImagePath = Base Path Where All Folders Can Be Found
@compressQuality = Quality Compression as % without %
@thumbnailCompressQuality = Quality Compression as % without %
@sizes = image sizes for creation
@thumbnailPrefix = prefix for files
*/
function processImages($images, $sizes, $baseImagePath, $baseSourcePath, $compressQuality, $thumbnailCompressQuality, $thumbnailPrefix){
	
	printMsgWithTag("Process Images", "h4");
	foreach ($images as $image){
		foreach ($sizes as $size){
			printMsgWithTag("Process Image: ".$image. " with size: ".$size, "span");
			// Main Image
			editAndSafeImage($image, $baseSourcePath, $baseImagePath, $compressQuality, $size);
			// Thumbnail
			editAndSafeImage($image, $baseSourcePath, $baseImagePath, $thumbnailCompressQuality, $size, $thumbnailPrefix);
		}
	}
}

/* Function editAndSafeImage 
@image = File Name
@baseSourcePath = Base Path Where All Images Can Be Found
@compressQuality = Quality Compression as % without %
@size = image size 
@prefix = prefix for files
*/
function editAndSafeImage($image, $baseSourcePath, $baseImagePath, $compressQuality, $size, $prefix = ""){
	$imagick = new \Imagick(realpath($baseSourcePath."/".$image));
	// Progressive
	$imagick->setInterlaceScheme(Imagick::INTERLACE_PLANE);
	// Compression
	$imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
	$imagick->setImageCompressionQuality($compressQuality);
	// Scale To Size
	$imagick->scaleImage($size, 0);
	// Set Extension
	$imagick->setImageFormat("jpeg");
	file_put_contents($baseImagePath.$size."/".$prefix.$image, $imagick);
}

/* Function scanSourcePath 
@baseSourcePath = Base Path Where All Images Can Be Found
@excludeList = List of Strings Which Should Be Ignored
*/
function scanSourcePath($baseSourcePath, $excludeList){
	printMsgWithTag("Scan Sourcepath For Images", "h4");
	$imageList = array ();	
	
	//Scan Sourcepath For Images To Process
	if ($handle = opendir($baseSourcePath)) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != "..") {
				$tempData = strstr($entry, '.', true);
				if (in_array($tempData, $excludeList)) {
					// do nothing here
				} else {
					array_push($imageList, $entry);
				}
			}
		}
		closedir($handle);
		$imageAmount = count($imageList);
		$tempMsg = ($imageAmount >= 2) ? "$imageAmount Images." : "$imageAmount Image";
		printMsgWithTag("Found ". $tempMsg, "span");
	}
	return $imageList;
}


/*
Function validatePath
@createIfNotExist = Create Folder If Not Exist
@rights = Folder Rights 
@path = Picturepath
@clearPath = Leere Verzeichnis
*/
function validatePath($createIfNotExist, $rights, $path, $clearPath = false){
	// Folder Exist?
	if(!file_exists($path)){
		// Check If Folder Should Be Created
		if($createIfNotExist){
			mkdir($path, $rights);
			// Print Msg
			printMsgWithTag("No valid path found. Created: \"".$path."\"", "span");
		} else {
			// Print Msg
			printMsgWithTag("No valid path found. Defined: \"".$path."\"", "span");
		}
	} else {
		// Print Msg
		printMsgWithTag("Valid path found. Defined is: \"".$path."\"", "span");
		if($clearPath){
			$files = glob($path."/*"); // get all file names present in folder
			foreach($files as $file){ // iterate files
			  if(is_file($file))
				unlink($file); // delete the file
			}
		}
	}
}

/* printMsgWithTag
@message = Message To Print
@tag = html tag to print
*/
function printMsgWithTag($message, $tag = "h4"){
	$debug = false;
	if($debug == true){
		// Print Msg
		if(!tag){
			echo "<h4>".$message."</h4><br>";
		} else {
			echo "<".$tag.">".$message."</".$tag."><br>";
		}
	}
}
?>