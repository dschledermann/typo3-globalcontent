<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$tempColumnsTtContent = [
	'tx_globalcontent_link' => [
		'exclude' => 0,
		'label' => 'Test',
		'config' => [
			'type' => 'passthrough',
			'size' => 30,
		]
	],
	'tx_globalcontent_orgurl' => [
		'exclude' => 0,
		'label' => 'Orginal url',
		'config' => [
			'type' => 'passthrough',
			'size' => 30,
		]
	],
	'tx_globalcontent_fetcher' => [
		'exclude' => 0,
		'label' => 'Fetcher',
		'config' => [
			'type' => 'passthrough',
			'size' => 30,
		]
	],
	"tx_globalcontent" => [
		"exclude" => 0,
		"label" => "LLL:EXT:" . $_EXTKEY . "/locallang.xlf:pi_title",
		"config" => [
			"type" => "user",
			"userFunc" => 'Linkfactory\\Globalcontent\\Hooks\\Userfuncs->main',
		],
	],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', $tempColumnsTtContent);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, "static/", "Global Content Page types");

$TCA['tt_content']['types'][$_EXTKEY . '_pi1']['showitem'] = 'CType;;4;button;1-1-1, header;;3;;2-2-2,tx_globalcontent,tx_globalcontent_link,tx_globalcontent_orgurl,tx_globalcontent_fetcher';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(array('LLL:EXT:globalcontent/locallang_db.xlf:tt_content.CType_pi1', $_EXTKEY . '_pi1'), 'CType');

// Add plugin to list of plugins in backend (when adding plugin).
if (TYPO3_MODE == 'BE')   {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['Linkfactory\\Globalcontent\\Hooks\Wizicon'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Classes/Hooks/Wizicon.php';
}
