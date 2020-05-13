<?php
	namespace Cache;
	function etag($key) {
		if(file_exists(__DIR__."/cache/$key.etag")) {
			return file_get_contents(__DIR__."/cache/$key.etag");
		}
		return false;
	}

	function update($key, $etag, $inhalt) {
		file_put_contents(__DIR__."/cache/$key.etag", $etag);
		file_put_contents(__DIR__."/cache/$key.cache", $inhalt);
	}

	function get($key) {
		if(file_exists(__DIR__."/cache/$key.cache")) {
			return file_get_contents(__DIR__."/cache/$key.cache");
		}
		return false;
	}

	function handle($key, $headers, $inhalt) {
		if(($headers["status"] ?? array(""))[0] == "200 OK") {
			if(isset($headers["etag"])) {
				update($key, $headers["etag"][0], $inhalt);
				return $inhalt;
			}
		} else if(($headers["status"] ?? array(""))[0] == "304 Not Modified") {
			return get($key);
		}
	}
?>
