<?php
	if(!file_exists(__DIR__."/../tarball/{$matches[1]}")) {
		$tarball = "https://api.github.com/repos/$benztzer/$repo/tarball/{$matches[1]}";
		$out = fopen(__DIR__."/../tarball/{$matches[1]}",'w');
		$curl = curl_init();
		$curlConfig = array(
			CURLOPT_URL             => $tarball,
			CURLOPT_FOLLOWLOCATION  => true,
			CURLOPT_FILE            => $out,
			CURLOPT_HTTPHEADER      => array(
				"Content-Type: application/json",
				"Authorization: token $OAuth",
				"User-Agent: ".$_SERVER["HTTP_USER_AGENT"],
			)
		);
		curl_setopt_array($curl, $curlConfig);
		curl_exec($curl);
		curl_close($curl);
	}
	header('Content-Type: application/octet-stream');
	header("Content-Transfer-Encoding: Binary");
	header("Content-disposition: attachment; filename=\"" . basename(__DIR__."/../tarball/{$matches[1]}") . "\"");
	readfile(__DIR__."/../tarball/{$matches[1]}");
?>
