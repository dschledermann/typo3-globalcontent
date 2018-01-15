<?php

namespace Linkfactory\Globalcontent;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class Cache {

	/**
	 * Get instance of TYPO3 Caching Framework.
	 *
	 * @return object TYPO3 Caching Framework.
	 */
	public static function getTYPO3CacheInstance() {
		$cacheIdentifier = "globalcontent_cache";

		// Initialize TYPO3 cache caching framework.
		if (version_compare(TYPO3_version, '8.0', '<')) {
			\TYPO3\CMS\Core\Cache\Cache::initializeCachingFramework();
		}

		try {
			$cacheInstance = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager')->getCache($cacheIdentifier);
		} catch (\TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException $e) {
			$cacheInstance = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager')->createCache($cacheIdentifier);
		}
		return $cacheInstance;
	}
}
