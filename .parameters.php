<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Iblock\TypeTable;
use \Bitrix\Main\Loader;
use \Bitrix\Iblock\IblockTable;

Loc::loadMessages(__FILE__);
if (!Loader::includeModule("iblock")) {
    ShowError(Loc::getMessage('IBLOCK_MODULE_NOT_INSTALLED'));
    return;
}
$IBlockTypes = [];
$arIBlockType = TypeTable::getList(
    array(
        'order' => array('NAME' => 'ASC'),
        'select' => array('*', 'NAME' => 'LANG_MESSAGE.NAME'),
        'filter' => array('=LANG_MESSAGE.LANGUAGE_ID' => 'ru')
    )
);
while ($arr = $arIBlockType->Fetch()) {
    $IBlockTypes[$arr["ID"]] = "[" . $arr["ID"] . "] " . $arr["NAME"];
}
$arIBlock = [];
$rsIBlock = IblockTable::GetList(['filter' => ['IBLOCK_TYPE_ID' => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE" => "Y"]]);
while ($arr = $rsIBlock->Fetch()) {
    $arIBlock[$arr["ID"]] = "[" . $arr["ID"] . "] " . $arr["NAME"];
}

$arComponentParameters = array(
    "GROUPS" => [],
    "PARAMETERS" => [
        "IBLOCK_TYPE" => array(
            "PARENT" => "BASE",
            "NAME" => "Тип информационного блока",
            "TYPE" => "LIST",
            "VALUES" => $IBlockTypes,
            "REFRESH" => "Y",
        ),
        "IBLOCK_ID" => array(
            "PARENT" => "BASE",
            "NAME" => "Информационный блок",
            "TYPE" => "LIST",
            "VALUES" => $arIBlock,
            "REFRESH" => "Y",
            "ADDITIONAL_VALUES" => "Y",
        ),
        "TASKS_COUNT" => array(
            "PARENT" => "BASE",
            "NAME" => "Число задач на странице",
            "TYPE" => "STRING",
            "DEFAULT" => "10",
        ),
        "VARIABLE_ALIASES" => array(
            "ELEMENT_ID" => array("NAME" => Loc::getMessage("TASK_TASK_ID")),
        ),
        "SEF_MODE" => array(
            "tasks" => array(
                "NAME" => Loc::getMessage("TASK_LIST_PAGE"),
                "DEFAULT" => "",
                "VARIABLES" => array(),
            ),
            "add" => array(
                "NAME" => Loc::getMessage("TASK_ADD_PAGE"),
                "DEFAULT" => "add/",
                "VARIABLES" => array(),
            ),
            "edit" => array(
                "NAME" => Loc::getMessage("TASK_EDIT_PAGE"),
                "DEFAULT" => "edit/#ELEMENT_ID#/",
                "VARIABLES" => array(),
            ),
        ),
        "CACHE_TIME"  =>  array("DEFAULT" => 36000000),
        "SET_TITLE" => [],
    ],
);
