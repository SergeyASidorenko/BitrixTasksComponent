<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
CJSCore::Init(['popup']);
$folder = '';
if ($arParams["SEF_MODE"] == "Y") {
      $folder = $arResult["FOLDER"];
}
?>
<div id="tasks">
      <p id="error"></p>
      <a class="task_add" href="<?= $arResult["FOLDER"] . $arResult["URLS"]["add"] ?>" title=""><?= Loc::getMessage("ADD_BUTTON_TEXT") ?></a>
      <? $tasks = $arResult["ITEMS"]; ?>
      <? if (empty($tasks)) : ?>
            <p><?= Loc::getMessage("NO_TASKS_MSG") ?></p>
      <? else : ?>
            <? $APPLICATION->IncludeComponent(
                  "bitrix:main.pagenavigation",
                  "",
                  array(
                        "NAV_OBJECT" => $arResult['NAV'],
                        "SEF_MODE" => "Y",
                  ),
                  false
            ); ?>

            <? foreach ($tasks as $task_id => $task) : ?>
                  <div class="task_container">
                        <div class="task_options">
                              <span class="option"><a class="edit" href="<?= $task["URL_EDIT"] ?>" title="">&#9999;</a></span>
                              <span class="option"><a class="delete" data_el_id="<?= $task_id ?>" href="<?= $arResult["FOLDER"] . $arResult["URLS"]["delete"] ?>" title="">&#10060;</a></span>
                              <span class="option"><a class="status" data_el_id="<?= $task_id ?>" data_status="<?= (int) $task["STATUS"] ?>" href="<?= $arResult["FOLDER"] . $arResult["URLS"]["status"] ?>" title=""><?= (int) $task["STATUS"] > 0 ? '&#128164;' : '&#10004;'; ?></a></span>
                        </div>
                        <div class="task <?= (int) $task["STATUS"] == 0 ? 'active' : '' ?>">
                              <div class="task_summary">
                                    <h3><?= $task["NAME"] ?></h3>
                              </div>
                              <div class="task_info">
                                    <p><span><strong><?= Loc::getMessage("COMMENT_LABEL") ?> </strong></span><span><?= $task["COMMENT"]; ?></span></p>
                                    <p><span><strong><?= Loc::getMessage("STATUS_LABEL") ?> </strong></span><span class="status_info"><?= (int) $task["STATUS"] > 0 ? 'Выполнено' : 'Невыполнено'; ?> </span></p>
                                    <?
                                    $targetDateString = '';
                                    if (!empty($task["TARGET_DATE"])) {
                                          $targetDate = DateTime::createFromFormat('Y-m-d H:i:s', $task["TARGET_DATE"]);
                                          $targetDateString = $targetDate->format('d.m.Y H:i:s');
                                    } ?>
                                    <p><span><strong><?= Loc::getMessage("TARGET_DATE_LABEL") ?> </strong></span><span><?= $targetDateString ?></span></p>
                                    <p><span><strong><?= Loc::getMessage("CREATED_LABEL") ?> </strong></span><span><?= $task["CREATED"]; ?></span></p>
                              </div>
                        </div>
                  </div>
            <? endforeach ?>
      <? endif ?>
</div>