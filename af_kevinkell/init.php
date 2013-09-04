<?php
/* This is taken from af_dilbert and adapted for Kevin & Kell */
class Af_KevinKell extends Plugin {
	private $host;

	function about() {
		return array(1.0,
			"Embeds Kevin and Kell strips",
			"158");
	}

	function init($host) {
		$this->host = $host;

		$host->add_hook($host::HOOK_ARTICLE_FILTER, $this);
	}

	function hook_article_filter($article) {
		$owner_uid = $article["owner_uid"];

		if (strpos($article["link"], "kevinandkell.com") !== FALSE) {
			if (strpos($article["plugin_data"], "kevinandkell,$owner_uid:") === FALSE) {
				$doc = new DOMDocument();
				@$doc->loadHTML(fetch_file_contents($article["link"]));

				$basenode = false;

				if ($doc) {
					$xpath = new DOMXPath($doc);
					$entries = $xpath->query('(//img[@alt=\'Comic Strip\'])');

					$matches = array();

					foreach ($entries as $entry) {
							$entry->setAttribute("src",
								rewrite_relative_url("http://www.kevinandkell.com/", $entry->getAttribute("src")));

							$basenode = $entry;
							break;
					}

					if ($basenode) {
						$article["content"] = $doc->saveXML($basenode);
						$article["plugin_data"] = "kevinandkell,$owner_uid:" . $article["plugin_data"];
					}
				}
			} else if (isset($article["stored"]["content"])) {
				$article["content"] = $article["stored"]["content"];
			}
		}

		return $article;
	}

	function api_version() {
		return 2;
	}

}
?>
