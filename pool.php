<?php
	$benztzer = "oxydon";
	$repo			= "BGSchorndorf";
	$OAuth 		= "50f99893f3ea8d697741109ab2ea7060310ce4cf";
	$url = $_GET["url"] ?? "no";

	include_once(__DIR__."/cache.php");

	if($url == "releases/latest") {
		include_once(__DIR__."/aktionen/releases_latest.php");
	}
	else if($url == "contents/version/versionen.yml") {
		include_once(__DIR__."/aktionen/versionen_yml.php");
	}
	else if($url == "contents/wiki.yml") {
		include_once(__DIR__."/aktionen/wiki_yml.php");
	}
	else if(preg_match("/tarball\/((?:[0-9]+)(?:\\.[0-9]+)*)/", $url, $matches) == 1) {
		include_once(__DIR__."/aktionen/tarball.php");
	}
	else {
		echo "FEHLER";
	}

	function curl($url, $header, &$rheader = array()) {
		$curl = curl_init();
		$curlConfig = array(
			CURLOPT_URL             => $url,
			CURLOPT_RETURNTRANSFER  => true,
			CURLOPT_HTTPHEADER      => $header,
			CURLOPT_HEADERFUNCTION	=> function($curl, $header) use (&$rheader) {
				$len = strlen($header);
				$header = explode(':', $header, 2);
				if (count($header) < 2) {
					return $len;
				}
				$rheader[strtolower(trim($header[0]))][] = trim($header[1]);
				return $len;
				}
		);
		curl_setopt_array($curl, $curlConfig);
		$r = curl_exec($curl);
		curl_close($curl);
		return $r;
	}
?>
