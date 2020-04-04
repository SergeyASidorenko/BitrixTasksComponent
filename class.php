<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponent $this */
/** Тестовое задание */
/** @package home */
/** @subpackage */
/** @global CUser $USER */
/** @copyright 2020 Home */

use \Bitrix\Iblock\ElementTable;
use \Bitrix\Iblock\PropertyTable;
use \Bitrix\Main\Entity\ReferenceField;
use \Bitrix\Main\ORM\Query;
use \Bitrix\Iblock\ElementPropertyTable;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\Response\AjaxJson;
use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Engine\ActionFilter;

Loc::loadMessages(__FILE__);

if (!\Bitrix\Main\Loader::includeModule('iblock')) {
    ShowError(Loc::getMessage('IBLOCK_MODULE_NOT_INSTALLED'));
    return;
}

class CPersonalTask extends CBitrixComponent implements Controllerable
{
    // код шаблона
    private $componentPage;
    // массив со значениями макросов из адреса запросов
    private $arVariables;

    public function onPrepareComponentParams($arParams)
    {
        if (!isset($arParams["CACHE_TIME"]))
            $arParams["CACHE_TIME"] = 36000000;
        if (strlen($arParams["IBLOCK_TYPE"]) <= 0)
            $arParams["IBLOCK_TYPE"] = "tasks";
        return $arParams;
    }
    public function configureActions()
    {
        return [
            'deleteTask' => [
                'prefilters' => [
                    new ActionFilter\Authentication,
                ],
            ],
            'setTaskStatus' => [
                'prefilters' => [
                    new ActionFilter\Authentication,
                ],
            ]
        ];
    }
    /**
     * Метод удаления элемента инфоблока по его коду
     *
     * @param int $elementID код элемента инфоблока
     *
     * @return bool
     */
    public function deleteTaskAction($elementID)
    {
        $this->ajaxMode = true;
        $errorCollection = null;
        // Почему-то не работала проверка на метод POST из метода configureActions()
        if (!$this->request->isPost()) {
            $error = new Error('Неверный запрос');
            $errorCollection = new ErrorCollection([$error]);
        }
        if ((int) $elementID <= 0) {
            $error = new Error('Неверный параметр');
            $errorCollection = new ErrorCollection([$error]);
        }
        if (!CIblockElement::delete($elementID)) {
            $error = new Error('Ошибка удаления');
            $errorCollection = new ErrorCollection([$error]);
        }
        if ($errorCollection) {
            return AjaxJson::createError($errorCollection);
        }
        return true;
    }
    /**
     * Метод обновления свойств элемента инфоблока по его коду
     *
     * @param int $elementID код элемента инфоблока
     * @param int $status код статуса (0 -задача не выполнена, 1 - выполнена)
     *
     * @return bool
     */
    public function setTaskStatusAction($elementID, $status)
    {
        $this->ajaxMode = true;
        $errorCollection = null;
        $element = new CIBlockElement;
        // Почему-то не работала проверка на метод POST из метода configureActions()
        if (!$this->request->isPost()) {
            $error = new Error('Неверный запрос');
            $errorCollection = new ErrorCollection([$error]);
        }
        if ((int) $elementID <= 0) {
            $error = new Error('Неверный параметр');
            $errorCollection = new ErrorCollection([$error]);
        }
        if (!$element->Update($elementID, ['PROPERTY_VALUES' => ['STATUS' => $status]])) {
            $error = new Error('Ошибка обновления');
            $errorCollection = new ErrorCollection([$error]);
        }
        if ($errorCollection) {
            return AjaxJson::createError($errorCollection);
        }
        return true;
    }
    /**
     * Метод получения адреса запрашиваемой страницы без параметров
     *
     * @return string
     */
    public function getCurrentPage()
    {
        return $this->request->getRequestedPage();
    }
    /**
     * Метод определения кода шаблона, который необходимо подключить компоненту
     *
     * @return void
     */
    private function guessTemplate()
    {
        // данный участок кода был скопирован 
        // из компонента news,
        // естественно все, что я посчитал ненужным моему компоненту
        // было удалено,
        // но наверняка здесь есть лишние участки кода,
        // в частности вызовы методов addGreedyPart, setResolveCallback
        $arDefaultSEFUrlTemplates = array(
            "tasks" => "",
            "add" => "add/",
            "edit" => "edit/#ELEMENT_ID#/",
        );
        $arDefaultSEFVariableAliases = array();

        $arDefaultVariableAliases = array();

        $arComponentVariables = array(
            "ELEMENT_ID",
            "ELEMENT_CODE",
            "ACTION"
        );
        $currentURI = $this->request->getRequestUri();
        $currentPage = $this->request->getRequestedPage();
        if ($this->arParams["SEF_MODE"] == "Y") {
            $this->arVariables = array();
            $arUrlTemplates = CComponentEngine::makeComponentUrlTemplates($arDefaultSEFUrlTemplates, $this->arParams["SEF_URL_TEMPLATES"]);
            $arVariableAliases = CComponentEngine::makeComponentVariableAliases($arDefaultSEFVariableAliases, $this->arParams["VARIABLE_ALIASES"]);

            $engine = new CComponentEngine($this);
            $engine->addGreedyPart("#SECTION_CODE_PATH#");
            $engine->setResolveCallback(array("CIBlockFindTools", "resolveComponentEngine"));
            $this->componentPage = $engine->guessComponentPath(
                $this->arParams["SEF_FOLDER"],
                $arUrlTemplates,
                $this->arVariables
            );
            $isWrongPage = false;
            if (!$this->componentPage) {
                $isWrongPage = true;
            }

            if ($isWrongPage) {
                $folder404 = str_replace("\\", "/", $this->arParams["SEF_FOLDER"]);
                if ($folder404 != "/")
                    $folder404 = "/" . trim($folder404, "/ \t\n\r\0\x0B") . "/";
                if (substr($folder404, -1) == "/")
                    $folder404 .= "index.php";

                if ($folder404 != $currentURI) {
                    \Bitrix\Iblock\Component\Tools::process404(
                        "",
                        ($this->arParams["SET_STATUS_404"] === "Y"),
                        ($this->arParams["SET_STATUS_404"] === "Y"),
                        ($this->arParams["SHOW_404"] === "Y"),
                        $this->arParams["FILE_404"]
                    );
                }
            }
            CComponentEngine::initComponentVariables($this->componentPage, $arComponentVariables, $arVariableAliases, $this->arVariables);
            $this->arResult = array(
                "FOLDER" =>  $this->arParams["SEF_FOLDER"],
                "URLS" => $arUrlTemplates,
            );
        } else {
            $arVariableAliases = CComponentEngine::makeComponentVariableAliases($arDefaultVariableAliases,  $this->arParams["VARIABLE_ALIASES"]);
            CComponentEngine::initComponentVariables(false, $arComponentVariables, $arVariableAliases, $this->arVariables);
            $this->componentPage = "";
            if (isset($this->arVariables["ACTION"])) {
                $this->componentPage = $this->arVariables["ACTION"];
                if (isset($this->arVariables["ELEMENT_ID"]) && intval($this->arVariables["ELEMENT_ID"]) > 0)
                    $this->componentPage = "edit";
                elseif (isset($this->arVariables["ELEMENT_CODE"]) && strlen($this->arVariables["ELEMENT_CODE"]) > 0)
                    $this->componentPage = "edit";
            } else
                $this->componentPage = "tasks";
            $this->arResult = array(
                "FOLDER" => "",
                "URLS" => array(
                    "add" => $currentPage . "?ACTION=add",
                    "edit" => $currentPage . "?ACTION=edit&" . $arVariableAliases["ELEMENT_ID"] . "=#ELEMENT_ID#",
                )
            );
        }
        // Делаем управление кодами шаблона,
        // что передать управление правильному файлу шаблона в конце концов
        if (
            $this->componentPage ==  $this->arParams["IBLOCK_TYPE"] ||
            $this->componentPage == 'status' ||
            $this->componentPage == 'delete'
        ) {
            $this->componentPage = '';
        }
        if ($this->componentPage == "add" || ($this->componentPage == "edit" && !isset($_REQUEST['CODE']))) {
            // Оказалось, что стандартный компонент добавления/редактирования формы
            // при редактировании принимает параметр кода элемента через массив $_REQUEST
            if ($this->componentPage == "edit" && !isset($_REQUEST['CODE'])) {
                $_REQUEST['CODE'] = $this->arVariables["ELEMENT_ID"];
            }
            $this->componentPage = 'add_edit';
        }
    }
    /**
     * Метод построения списка задач (отрабатывает только, если код шаблона соответствуют списку задач)
     *
     * @return void
     */
    public function buildList()
    {
        global $USER;
        $this->arResult['ITEMS'] = [];
        // Созданием цепочку навигации
        $nav = new PageNavigation("nav-more-tasks");
        $nav->allowAllRecords(true)
            ->setPageSize($this->arParams['TASKS_COUNT'])
            ->initFromUri();
        // Находим все задачи текущего пользователя,
        // для них далее формируем подзапросы в БД,
        // для получения свойств элементов
        $elementsResult = ElementTable::GetList([
            'order' => ['DATE_CREATE' => 'DESC'],
            'filter' => ["IBLOCK_ID" => $this->arParams["IBLOCK_ID"], 'ACTIVE' => 'Y', 'CREATED_BY' => $USER::GetID()],
            'select' => ['ID', 'DATE_CREATE', 'NAME', 'PREVIEW_TEXT'],
            "count_total" => true,
            "offset" => $nav->getOffset(),
            "limit" => $nav->getLimit(),
        ]);
        $nav->setRecordCount($elementsResult->getCount());
        $elements = $elementsResult->fetchAll();
        foreach ($elements as $element) {
            $properties = ElementPropertyTable::GetList([
                'filter' => ["IBLOCK_ELEMENT_ID" => $element['ID']],
                'select' => ['CODE' => 'IBLOCK_PROPERTY.CODE', 'VALUE'],
                'runtime' => [
                    new ReferenceField(
                        'IBLOCK_PROPERTY',
                        PropertyTable::class,
                        Query\Join::on('ref.ID', 'this.IBLOCK_PROPERTY_ID')
                    )
                ],
            ])->fetchAll();
            // Формируем данные для шаблона списка задач, в том числе и адреса редактирования задач
            $this->arResult['ITEMS'][$element['ID']]['NAME'] = $element['NAME'];
            $this->arResult['ITEMS'][$element['ID']]['COMMENT'] = $element['PREVIEW_TEXT'];
            $this->arResult['ITEMS'][$element['ID']]['CREATED'] = $element['DATE_CREATE'];
            $this->arResult['ITEMS'][$element['ID']]['URL_EDIT'] = CComponentEngine::MakePathFromTemplate($this->arResult['URLS']['edit'], ['ELEMENT_ID' => $element['ID']]);
            // Адреса для страниц удаления и редактирования задач устанавливаем произвольные, 
            // так как для них встроена поддержа AJAX запросов с использованием библиотеки BX
            $this->arResult['URLS']['delete'] = '#';
            $this->arResult['URLS']['status'] = '#';
            foreach ($properties as $property) {
                $this->arResult['ITEMS'][$element['ID']][$property['CODE']] = $property['VALUE'];
            }
        }
        $this->arResult['NAV'] = $nav;
    }
    public function executeComponent()
    {
        global $USER;
        if ($this->startResultCache(false, $USER->GetID(), false)) {
            $this->guessTemplate();
            if (empty($this->componentPage)) {
                $this->buildList();
            }
            // если текущий шаблон - форма добавления/редактирования
            // то делаем запрос на формирования списка свойств элемента инфоблока,
            // которые нужно отобразить в форме
            if ($this->componentPage == 'add_edit') {
                $properties = PropertyTable::GetList([
                    'filter' => ["IBLOCK_ID" => $this->arParams["IBLOCK_ID"], 'IS_REQUIRED' => 'Y'],
                    'select' => ['ID'],
                ])->fetchAll();
                $this->arResult['EDITABLE_PROPS'] = ["NAME", "PREVIEW_TEXT"];
                foreach ($properties as $property) {
                    $this->arResult['EDITABLE_PROPS'][] = $property['ID'];
                }
            }

            $this->includeComponentTemplate($this->componentPage);
        }
    }
}
