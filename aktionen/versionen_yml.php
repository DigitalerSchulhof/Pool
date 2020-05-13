<?php
	$headers = array();
	$etag = Cache\etag("versionen_yml");
	$sheaders = array(
		"Content-Type: application/json",
		"Authorization: token $OAuth",
		"User-Agent: ".$_SERVER["HTTP_USER_AGENT"],
		"Accept: application/vnd.github.VERSION.raw",
	);
	if($etag !== false) {
		$sheaders[] = "If-None-Match: $etag";
	}
	$r = curl("https://api.github.com/repos/$benztzer/$repo/contents/version/versionen.yml?ref=master", $sheaders, $headers);
	echo Cache\handle("versionen_yml", $headers, $r);
?>
