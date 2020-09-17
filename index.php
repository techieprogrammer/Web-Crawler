<?php
include_once("config.php");
include_once("crawler.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Web Crawler</title>
	<style>
	.textbox
	{ 
		border: medium none;
		font-family: Arial,Sans-Serif;
		font-size: 16px;
		height:28px;
		line-height: 24px;
		width: 578px;
		border: 1px solid #ACBABD;
		padding:5px;
	}
	.submitbox 
	{	
		height:40px; 
		width:80px; 
		text-align:center;
	} 
	</style>
</head>
<body>
	<div style="padding:10px;">
		<div>
			<form name="crawlsearch" method="post" action="index.php">
			<table>
			<tr>
				<td align="center" colspan="2"><b>Web Crawl</b></td>
			</tr>
			<tr>
				<td>
					<input class="textbox" type="text" placeholder="Enter URL" name="url" value="<?php if(isset($_POST["url"])) { echo $_POST["url"]; } ?>">
				</td>
				<td>
					<input class="submitbox" type="submit" name= "SubmitBox" value="Crawl">
				</td>
			</tr>
			</table>
			</form>
		</div>
	</div>
	<div style="padding:10px;">
	<?php
	if(isset($_POST["SubmitBox"]))
	{
		$obj = new webCrawler();
		$obj->siteURL = $_POST["url"];	
		$returnData = $obj->parser();
		echo "<pre>";	
		print_r($returnData);
		echo "</pre>";
	}
	?>
	</div>
</body>
<html>
