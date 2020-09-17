<?php
//---- Href Element Syntax Start ----
$hrefTag = array();
$hrefTag[0] = "<a";
$hrefTag[1] = "href";
$hrefTag[2] = "=";
$hrefTag[3] = ">";
$hrefTagCountStart = 0;
$hrefTagCountFinal = count($hrefTag);
$hrefTagLengthStart = 0;
$hrefTagLengthFinal = strlen($hrefTag[0]);
$hrefTagPointer =& $hrefTag[0];
//---- Href Element Syntax End ----

//---- Image Element Syntax Start ----
$imgTag = array();
$imgTag[0] = "<img";
$imgTag[1] = "src";
$imgTag[2] = "=";
$imgTag[3] = ">";
$imgTagCountStart = 0;
$imgTagCountFinal = count($imgTag);
$imgTagLengthStart = 0;
$imgTagLengthFinal = strlen($imgTag[0]);
$imgTagPointer =& $imgTag[0];
//---- Image Element Syntax End ----

//---- Valid Domain Start -----
$Url_Extensions = array("asp","aspx","html","htm","php","php3","biz","com","edu","gov","info","int","jobs",
"net","org","in","us","uk");
//---- Valid Domain End -------

//---- Valid File Extension Start ----
$Document_Extensions = array("doc","pdf","ppt","txt");
$Image_Extensions = array("gif","jpeg","jpg","png");
//---- Valid File Extension End ------

//---- Curl Parameters Start ----
$crawlOptions = array(
CURLOPT_RETURNTRANSFER => true,     		// return web page
CURLOPT_HEADER         => false,    		// don't return headers
CURLOPT_FOLLOWLOCATION => true,     		// follow redirects
CURLOPT_ENCODING       => "",       		// handle all encodings
CURLOPT_USERAGENT      => "AlgoberryBot",	// who am i
CURLOPT_AUTOREFERER    => true,     		// set referer on redirect
CURLOPT_CONNECTTIMEOUT => 120,      		// timeout on connect
CURLOPT_TIMEOUT        => 120,      		// timeout on response
CURLOPT_MAXREDIRS      => 0,       			// stop after 10 redirects
);
//---- Curl Parameters End ------
?>
