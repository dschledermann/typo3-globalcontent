<?php

namespace Linkfactory\Globalcontent\Hooks;

/**
 * Class that adds the wizard icon.
 */
class Wizicon {

	/**
	 * Adds the newloginbox wizard icon
	 *
	 * @param array Input array with wizard items for plugins
	 * @return array Modified input array, having the item for newloginbox added.
	 */
	function proc($wizardItems)	{
		global $LANG;

		$ll = $this->includeLocalLang();
		$wizardItems['plugins_tx_globalcontent_pi1'] = array(
			'icon' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('globalcontent') . 'res/icons/network-server.png',
			'title' => $LANG->getLLL('pi_title', $ll),
			'description' => $LANG->getLLL('pi_plus_wiz_description', $ll),
			'params' => '&defVals[tt_content][CType]=globalcontent_pi1'
		);

		return $wizardItems;
                            
	}

	/**
	 * Includes the locallang file for the 'tt_news' extension
	 *
	 * @return array The LOCAL_LANG array
	 */
	function includeLocalLang()    {
		$llFile = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('globalcontent') . 'locallang.xlf';
		$parser = new \TYPO3\CMS\Core\Localization\Parser\XliffParser();
		return $parser->getParsedData($llFile, $GLOBALS['LANG']->lang);
	}
}
