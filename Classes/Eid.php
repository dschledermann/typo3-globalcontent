<?php

namespace Linkfactory\Globalcontent;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class to handle outside requests (eID).
 *
 * Instantiated at the bottom of this file.
 */
class Eid {

	const PAGE_TYPE_LIST = 9001;
	const PAGE_TYPE_SINGLE = 9002;

	/**
	 * Main.
	 *
	 * @return void
	 */
	public function main() {
		switch (GeneralUtility::_GET("mode")) {

			// Show remote page to choose element.
			case "chooseElement":
				$url = GeneralUtility::_GET("url");
				$this->chooseElement($url);
				break;

			// Receive chosen element-id.
			case "fetchElement":
				$fetchUrl = trim(GeneralUtility::_POST("fetchUrl"));
				$cid = intval(GeneralUtility::_POST("cid"));
				$elementId = intval(GeneralUtility::_POST("elementId"));
				$this->fetchElement($fetchUrl, $cid, $elementId);
				break;

			// Show element for preview in backend.
			case "showElement":
				$url = trim(GeneralUtility::_GET("url"));
				$this->showElement($url);
				break;

			default:
				// Sniff if we are trying to fetch an element using the old url
				if ($elementId = intval(GeneralUtility::_GET('elementId'))) {
					$this->legacyFetch($elementId);
				}
				else {
					die("No access");
				}
				break;

		}
	}

	/**
	 * Get an element from local installation using the legacy url scheme
	 * @param  integer  $elementId   Uid for element
	 * @return void
	 */
	private function legacyFetch($elementId) {
		global $TYPO3_DB;

		// The element should be fetched from the proper page in order for the new
		// scheme to work properly
		$rs = $TYPO3_DB->exec_SELECTquery('pid', 'tt_content', "uid = $elementId");
		list($pid) = $TYPO3_DB->sql_fetch_row($rs);

		// Use this to fetch with the proper url-scheme
		echo file_get_contents(GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST') . '/index.php?no_cache=1&type=9002&id=' . $pid . '&cid=' . $elementId);
	}

	/**
	 * Loads remote page based on type, for choosing element.
	 *
	 * @param string $url
	 * @return void
	 */
	private function chooseElement($url) {

		// Build url.
		$fetchUrl = $url;
		$url .= strpos($url, "?") > 0 ? "&" : "?";
		$url .= "type=" . self::PAGE_TYPE_LIST;
		$url .= "&no_cache=1";
		$url .= "&callbackUrl=" . urlencode($this->getSiteUrl() . "?eID=globalcontent&mode=fetchElement");
		$url .= "&fetchUrl=" . urlencode($fetchUrl);

		$content = file_get_contents($url);
		print($content);
	}

	/**
	 * Fetch single element from remote page.
	 *
	 * @param string $url
	 * @param number $cid
	 * @param number $elementId
	 * @return void
	 */
	private function fetchElement($url, $cid, $elementId) {

		// Build url to fetch element.
		$parameters = array(
			"type" => self::PAGE_TYPE_SINGLE,
			"no_cache" => 1,
			"cid" => $cid
		);
		$fetchUrl = $this->buildUrl($url, $parameters);

		// Build url to store.
		$parameters = array(
			"type" => self::PAGE_TYPE_SINGLE,
			"cid" => $cid
		);
		$url = $this->buildUrl($url, $parameters);

		$data = file_get_contents($fetchUrl);

		// Make sure utf8-encoding are removed and clean data.
		if (mb_detect_encoding($data, 'UTF-8', true) == 'UTF-8') {
			$data = utf8_decode($data);
		}
		$data = str_replace("\r", "", $data);
		$data = str_replace("\n", "", $data);
		$data = addslashes($data);

		print("<script type=\"text/javascript\">");
		print("window.opener.document.getElementById('test').innerHTML = '" . $data . "';");
		print("window.opener.document.getElementById('tx_globalcontent_link').value = '" . $url . "';");
		print("window.close();");
		print("</script>\n");
	}

	/**
	 * Show element.
	 *
	 * @param string $url
	 * @return void
	 */
	private function showElement($url) {
		$content = file_get_contents($url);
		print($content);
	}

	/**
	 * Return site-url
	 *
	 * @return string.
	 */
	private function getSiteUrl() {
		return GeneralUtility::getIndpEnv('TYPO3_SITE_URL');
	}

	/**
	 * Build url.
	 *
	 * @param string $url
	 * @param array $parameters
	 * @return string
	 */
	private function buildUrl($url, $parameters = array()) {
		if (count($parameters) > 0) {
			$paramString = "";
			foreach ($parameters as $name => $value) {
				$paramString .= $paramString != "" ? "&" : "";
				$paramString .= $name . "=" . urlencode($value);
			}
			if ($paramString != "") {
				$url .= strpos($url, "?") == 0 ? "?" : "&";
				$url .= $paramString;
			}
		}
		return $url;
	}
}
