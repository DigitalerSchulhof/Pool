<?php
	$benztzer = "oxydon";
	$repo			= "BGSchorndorf";
	$url = $_GET["url"] ?? "no";

	if($url == "releases/latest") {
		$headers = array();
		$etag = cache_etag("releases_latest");
		$sheaders = array(
			"Content-Type: application/json",
			"Authorization: token 50f99893f3ea8d697741109ab2ea7060310ce4cf",
			"User-Agent: ".$_SERVER["HTTP_USER_AGENT"],
			"Accept: application/vnd.github.v3+json",
		);
		if($etag !== false) {
			$sheaders[] = "If-None-Match: $etag";
		}
		$r = curl("https://api.github.com/repos/$benztzer/$repo/releases/latest", $sheaders, $headers);
		$r = cache_handle("releases_latest", $headers, $r);
		$r = preg_replace("/https:\\/\\/api.github.com\\/repos\\/$benztzer\\/$repo\\/tarball\\/((?:[0-9]+)(?:\\.[0-9]+)*)/", "https://pool.digitaler-schulhof.de/tarball/$1", $r);
		echo $r;
	}
	else if($url == "contents/version/versionen.yml") {
		$headers = array();
		$etag = cache_etag("versionen_yml");
		$sheaders = array(
			"Content-Type: application/json",
			"Authorization: token 50f99893f3ea8d697741109ab2ea7060310ce4cf",
			"User-Agent: ".$_SERVER["HTTP_USER_AGENT"],
			"Accept: application/vnd.github.VERSION.raw",
		);
		if($etag !== false) {
			$sheaders[] = "If-None-Match: $etag";
		}
		$r = curl("https://api.github.com/repos/$benztzer/$repo/contents/version/versionen.yml?ref=master", $sheaders, $headers);
		echo cache_handle("versionen_yml", $headers, $r);
	}
	else if(preg_match("/tarball\/((?:[0-9]+)(?:\\.[0-9]+)*)/", $url, $matches) == 1) {
		if(!file_exists("tarball/{$matches[1]}")) {
			$tarball = "https://api.github.com/repos/$benztzer/$repo/tarball/{$matches[1]}";
			$out = fopen("tarball/{$matches[1]}",'w');
			$curl = curl_init();
			$curlConfig = array(
				CURLOPT_URL             => $tarball,
				CURLOPT_FOLLOWLOCATION  => true,
				CURLOPT_FILE            => $out,
				CURLOPT_HTTPHEADER      => array(
					"Content-Type: application/json",
					"Authorization: token 50f99893f3ea8d697741109ab2ea7060310ce4cf",
					"User-Agent: ".$_SERVER["HTTP_USER_AGENT"],
				)
			);
			curl_setopt_array($curl, $curlConfig);
			curl_exec($curl);
			curl_close($curl);
		}
		header('Content-Type: application/octet-stream');
		header("Content-Transfer-Encoding: Binary");
		header("Content-disposition: attachment; filename=\"" . basename("tarball/{$matches[1]}") . "\"");
		readfile("tarball/{$matches[1]}");
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

	function cache_etag($key) {
		if(file_exists("cache/$key.etag")) {
			return file_get_contents("cache/$key.etag");
		}
		return false;
	}

	function cache_update($key, $etag, $inhalt) {
		file_put_contents("cache/$key.etag", $etag);
		file_put_contents("cache/$key.cache", $inhalt);
	}

	function cache_get($key) {
		if(file_exists("cache/$key.cache")) {
			return file_get_contents("cache/$key.cache");
		}
		return false;
	}

	function cache_handle($key, $headers, $inhalt) {
		if(($headers["status"] ?? array(""))[0] == "200 OK") {
			if(isset($headers["etag"])) {
				cache_update($key, $headers["etag"][0], $inhalt);
				return $inhalt;
			}
		} else if(($headers["status"] ?? array(""))[0] == "304 Not Modified") {
			return cache_get($key);
		}
	}
?>
