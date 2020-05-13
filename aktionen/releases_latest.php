<?php
	$headers = array();
	$etag = Cache\etag("releases_latest");
	$sheaders = array(
		"Content-Type: application/json",
		"Authorization: token $OAuth",
		"User-Agent: ".$_SERVER["HTTP_USER_AGENT"],
		"Accept: application/vnd.github.v3+json",
	);
	if($etag !== false) {
		$sheaders[] = "If-None-Match: $etag";
	}
	$r = curl("https://api.github.com/repos/$benztzer/$repo/releases/latest", $sheaders, $headers);
	$r = Cache\handle("releases_latest", $headers, $r);
	$r = preg_replace("/https:\\/\\/api.github.com\\/repos\\/$benztzer\\/$repo\\/tarball\\/((?:[0-9]+)(?:\\.[0-9]+)*)/", "https://pool.digitaler-schulhof.de/tarball/$1", $r);
	echo $r;
?>
