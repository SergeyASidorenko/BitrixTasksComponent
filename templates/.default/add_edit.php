<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
$APPLICATION->SetTitle(Loc::getMessage('ADD_TASK_FORM_TITLE'));
$listURL = '';
if ($arParams['SEF_MODE'] == 'Y') {
	$listURL = $arParams["SEF_FOLDER"];
} else {
	$listURL = $component->getCurrentPage();
}
?><? $APPLICATION->IncludeComponent(
		"bitrix:iblock.element.add.form",
		"",
		array(
			"CUSTOM_TITLE_DATE_ACTIVE_FROM" => "",
			"CUSTOM_TITLE_DATE_ACTIVE_TO" => "",
			"CUSTOM_TITLE_DETAIL_PICTURE" => "",
			"CUSTOM_TITLE_DETAIL_TEXT" => "",
			"CUSTOM_TITLE_IBLOCK_SECTION" => "",
			"CUSTOM_TITLE_NAME" => "",
			"CUSTOM_TITLE_PREVIEW_PICTURE" => "",
			"CUSTOM_TITLE_PREVIEW_TEXT" => "",
			"CUSTOM_TITLE_TAGS" => "",
			"DEFAULT_INPUT_SIZE" => "30",
			"DETAIL_TEXT_USE_HTML_EDITOR" => "N",
			"ELEMENT_ASSOC" => "CREATED_BY",
			"GROUPS" => array(),
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"LEVEL_LAST" => "Y",
			"LIST_URL" => $listURL,
			"MAX_FILE_SIZE" => "0",
			"MAX_LEVELS" => "100000",
			"MAX_USER_ENTRIES" => "100000",
			"PREVIEW_TEXT_USE_HTML_EDITOR" => "N",
			"PROPERTY_CODES" => $arResult['EDITABLE_PROPS'],
			"PROPERTY_CODES_REQUIRED" => $arResult['EDITABLE_PROPS'],
			"RESIZE_IMAGES" => "N",
			"SEF_MODE" => "N",
			"STATUS" => "ANY",
			"STATUS_NEW" => "N",
			"USER_MESSAGE_ADD" => Loc::getMessage('ADD_SUCCESS_MSG'),
			"USER_MESSAGE_EDIT" => Loc::getMessage('EDIT_SUCCESS_MSG'),
			"USE_CAPTCHA" => "N"
		),
		$component
	); ?><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>