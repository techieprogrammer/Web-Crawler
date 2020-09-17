<?php
class webCrawler
{
	public $siteURL;
	public $error;
	
	function __construct()
	{
		$this->siteURL = "";
		$this->error = "";
	}

	function parser()	
	{
		global $hrefTag,$hrefTagCountStart,$hrefTagCountFinal,$hrefTagLengthStart,$hrefTagLengthFinal,$hrefTagPointer;
		global $imgTag,$imgTagCountStart,$imgTagCountFinal,$imgTagLengthStart,$imgTagLengthFinal,$imgTagPointer;
		global $Url_Extensions,$Document_Extensions,$Image_Extensions,$crawlOptions;
					
		$dotCount = 0;
		$slashCount = 0;
		$singleSlashCount = 0;
		$doubleSlashCount = 0;
		$parentDirectoryCount = 0;

		$linkBuffer = array();
		
		if(($url = trim($this->siteURL)) != "")
		{
			$crawlURL = rtrim($url,"/");
			if(($directoryURL = dirname($crawlURL)) == "http:")
			{	$directoryURL = $crawlURL;	}
			$urlParser = preg_split("/\//",$crawlURL);
		
			//-- Curl Start --
			$curlObject = curl_init($crawlURL);
			curl_setopt_array($curlObject,$crawlOptions);
			$webPageContent = curl_exec($curlObject);
			$errorNumber = curl_errno($curlObject);
			curl_close($curlObject);
			//-- Curl End --
		
			if($errorNumber == 0)
			{
				$webPageCounter = 0;
				$webPageLength = strlen($webPageContent);
				while($webPageCounter < $webPageLength)
				{
					$character = $webPageContent[$webPageCounter];
					if($character == "")
					{	
						$webPageCounter++;	
						continue;
					}
					$character = strtolower($character);
					//-- Href Filter Start --
					if($hrefTagPointer[$hrefTagLengthStart] == $character)
					{
						$hrefTagLengthStart++;
						if($hrefTagLengthStart == $hrefTagLengthFinal)
						{
							$hrefTagCountStart++;
							if($hrefTagCountStart == $hrefTagCountFinal)
							{
								if($hrefURL != "")
								{
									if($parentDirectoryCount >= 1 || $singleSlashCount >= 1 || $doubleSlashCount >= 1)
									{
										if($doubleSlashCount >= 1)
										{	$hrefURL = "http://".$hrefURL;	}
										else if($parentDirectoryCount >= 1)
										{
											$tempData = 0;
											$tempString = "";
											$tempTotal = count($urlParser) - $parentDirectoryCount;
											while($tempData < $tempTotal)
											{
												$tempString .= $urlParser[$tempData]."/";
												$tempData++;
											}
											$hrefURL = $tempString."".$hrefURL;
										}
										else if($singleSlashCount >= 1)
										{	$hrefURL = $urlParser[0]."/".$urlParser[1]."/".$urlParser[2]."/".$hrefURL;	}
									}
									$host = "";
									$hrefURL = urldecode($hrefURL);
									$hrefURL = rtrim($hrefURL,"/");
									if(filter_var($hrefURL,FILTER_VALIDATE_URL) == true)
									{	
										$dump = parse_url($hrefURL);
										if(isset($dump["host"]))
										{	$host = trim(strtolower($dump["host"]));	}
									}
									else
									{
										$hrefURL = $directoryURL."/".$hrefURL;
										if(filter_var($hrefURL,FILTER_VALIDATE_URL) == true)
										{	
											$dump = parse_url($hrefURL);	
											if(isset($dump["host"]))
											{	$host = trim(strtolower($dump["host"]));	}
										}
									}
									if($host != "")
									{
										$extension = pathinfo($hrefURL,PATHINFO_EXTENSION);
										if($extension != "")
										{
											$tempBuffer ="";
											$extensionlength = strlen($extension);
											for($tempData = 0; $tempData < $extensionlength; $tempData++)
											{
												if($extension[$tempData] != "?")
												{	
													$tempBuffer = $tempBuffer.$extension[$tempData];
													continue;
												}
												else
												{
													$extension = trim($tempBuffer);
													break;
												}
											}
											if(in_array($extension,$Url_Extensions))
											{	$type = "domain";	}
											else if(in_array($extension,$Image_Extensions))
											{	$type = "image";	}
											else if(in_array($extension,$Document_Extensions))
											{	$type = "document";	}
											else
											{	$type = "unknown";	}
										}
										else
										{	$type = "domain";	}
									
										if($hrefURL != "")
										{
											if($type == "domain" && !in_array($hrefURL,$this->linkBuffer["domain"]))
											{	$this->linkBuffer["domain"][] = $hrefURL;	}
											if($type == "image" && !in_array($hrefURL,$this->linkBuffer["image"]))
											{	$this->linkBuffer["image"][] = $hrefURL;	}
											if($type == "document" && !in_array($hrefURL,$this->linkBuffer["document"]))
											{	$this->linkBuffer["document"][] = $hrefURL;	}
											if($type == "unknown" && !in_array($hrefURL,$this->linkBuffer["unknown"]))
											{	$this->linkBuffer["unknown"][] = $hrefURL;	}
										}
									}
								}
								$hrefTagCountStart = 0;
							}
							if($hrefTagCountStart == 3)
							{
								$hrefURL = "";
								$dotCount = 0;
								$slashCount = 0;
								$singleSlashCount = 0;
								$doubleSlashCount = 0;
								$parentDirectoryCount = 0;
								$webPageCounter++;
								while($webPageCounter < $webPageLength)
								{
									$character = $webPageContent[$webPageCounter];
									if($character == "")
									{	
										$webPageCounter++;	
										continue;
									}
									if($character == "\"" || $character == "'")
									{
										$webPageCounter++;
										while($webPageCounter < $webPageLength)
										{
											$character = $webPageContent[$webPageCounter];
											if($character == "")
											{	
												$webPageCounter++;	
												continue;
											}
											if($character == "\"" || $character == "'" || $character == "#")
											{	
												$webPageCounter--;	
												break;	
											}
											else if($hrefURL != "")
											{	$hrefURL .= $character;	}
											else if($character == "." || $character == "/")
											{
												if($character == ".")
												{
													$dotCount++;
													$slashCount = 0;
												}
												else if($character == "/")
												{
													$slashCount++;
													if($dotCount == 2 && $slashCount == 1)
													$parentDirectoryCount++;
													else if($dotCount == 0 && $slashCount == 1)
													$singleSlashCount++;
													else if($dotCount == 0 && $slashCount == 2)
													$doubleSlashCount++;
													$dotCount = 0;
												}
											}
											else
											{	$hrefURL .= $character;	}
											$webPageCounter++;
										}
										break;
									}
									$webPageCounter++;
								}
							}
							$hrefTagLengthStart = 0;
							$hrefTagLengthFinal = strlen($hrefTag[$hrefTagCountStart]);
							$hrefTagPointer =& $hrefTag[$hrefTagCountStart];
						}
					}
					else
					{	$hrefTagLengthStart = 0;	}
					//-- Href Filter End --
					//-- Image Filter Start --
					if($imgTagPointer[$imgTagLengthStart] == $character)
					{
						$imgTagLengthStart++;
						if($imgTagLengthStart == $imgTagLengthFinal)
						{
							$imgTagCountStart++;
							if($imgTagCountStart == $imgTagCountFinal)
							{
								if($imgURL != "")
								{
									if($parentDirectoryCount >= 1 || $singleSlashCount >= 1 || $doubleSlashCount >= 1)
									{
										if($doubleSlashCount >= 1)
										{	$imgURL = "http://".$imgURL;	}
										else if($parentDirectoryCount >= 1)
										{
											$tempData = 0;
											$tempString = "";
											$tempTotal = count($urlParser) - $parentDirectoryCount;
											while($tempData < $tempTotal)
											{
												$tempString .= $urlParser[$tempData]."/";
												$tempData++;
											}
											$imgURL = $tempString."".$imgURL;
										}
										else if($singleSlashCount >= 1)
										{	$imgURL = $urlParser[0]."/".$urlParser[1]."/".$urlParser[2]."/".$imgURL;	}
									}
									$host = "";
									$imgURL = urldecode($imgURL);
									$imgURL = rtrim($imgURL,"/");
									if(filter_var($imgURL,FILTER_VALIDATE_URL) == true)
									{	
										$dump = parse_url($imgURL);	
										$host = trim(strtolower($dump["host"]));
									}
									else
									{
										$imgURL = $directoryURL."/".$imgURL;
										if(filter_var($imgURL,FILTER_VALIDATE_URL) == true)
										{	
											$dump = parse_url($imgURL);	
											$host = trim(strtolower($dump["host"]));
										}	
									}
									if($host != "")
									{
										$extension = pathinfo($imgURL,PATHINFO_EXTENSION);
										if($extension != "")
										{
											$tempBuffer ="";
											$extensionlength = strlen($extension);
											for($tempData = 0; $tempData < $extensionlength; $tempData++)
											{
												if($extension[$tempData] != "?")
												{	
													$tempBuffer = $tempBuffer.$extension[$tempData];
													continue;
												}
												else
												{
													$extension = trim($tempBuffer);
													break;
												}
											}
											if(in_array($extension,$Url_Extensions))
											{	$type = "domain";	}
											else if(in_array($extension,$Image_Extensions))
											{	$type = "image";	}
											else if(in_array($extension,$Document_Extensions))
											{	$type = "document";	}
											else
											{	$type = "unknown";	}
										}
										else
										{	$type = "domain";	}
									
										if($imgURL != "")
										{
											if($type == "domain" && !in_array($imgURL,$this->linkBuffer["domain"]))
											{	$this->linkBuffer["domain"][] = $imgURL;	}
											if($type == "image" && !in_array($imgURL,$this->linkBuffer["image"]))
											{	$this->linkBuffer["image"][] = $imgURL;	}
											if($type == "document" && !in_array($imgURL,$this->linkBuffer["document"]))
											{	$this->linkBuffer["document"][] = $imgURL;	}
											if($type == "unknown" && !in_array($imgURL,$this->linkBuffer["unknown"]))
											{	$this->linkBuffer["unknown"][] = $imgURL;	}
										}
									}
								}
								$imgTagCountStart = 0;
							}
							if($imgTagCountStart == 3)
							{
								$imgURL = "";
								$dotCount = 0;
								$slashCount = 0;
								$singleSlashCount = 0;
								$doubleSlashCount = 0;
								$parentDirectoryCount = 0;
								$webPageCounter++;
								while($webPageCounter < $webPageLength)
								{
									$character = $webPageContent[$webPageCounter];
									if($character == "")
									{	
										$webPageCounter++;	
										continue;
									}
									if($character == "\"" || $character == "'")
									{
										$webPageCounter++;
										while($webPageCounter < $webPageLength)
										{
											$character = $webPageContent[$webPageCounter];
											if($character == "")
											{	
												$webPageCounter++;	
												continue;
											}
											if($character == "\"" || $character == "'" || $character == "#")
											{	
												$webPageCounter--;	
												break;	
											}
											else if($imgURL != "")
											{	$imgURL .= $character;	}
											else if($character == "." || $character == "/")
											{
												if($character == ".")
												{
													$dotCount++;
													$slashCount = 0;
												}
												else if($character == "/")
												{
													$slashCount++;
													if($dotCount == 2 && $slashCount == 1)
													$parentDirectoryCount++;
													else if($dotCount == 0 && $slashCount == 1)
													$singleSlashCount++;
													else if($dotCount == 0 && $slashCount == 2)
													$doubleSlashCount++;
													$dotCount = 0;
												}
											}
											else
											{	$imgURL .= $character;	}
											$webPageCounter++;
										}
										break;
									}
									$webPageCounter++;
								}
							}
							$imgTagLengthStart = 0;
							$imgTagLengthFinal = strlen($imgTag[$imgTagCountStart]);
							$imgTagPointer =& $imgTag[$imgTagCountStart];
						}
					}
					else
					{	$imgTagLengthStart = 0;	}
					//-- Image Filter End --
					$webPageCounter++;
				}
			}
			else
			{	$this->error = "Unable to proceed, permission denied";	}
		}
		else
		{	$this->error = "Please enter url";	}
	
		if($this->error != "")
		{	$this->linkBuffer["error"] = $this->error;	}
		
		return $this->linkBuffer;
	}	
}
?>
