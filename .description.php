<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
$arComponentDescription = [
    "NAME" => "Список личных задач",
    "DESCRIPTION" => "Компонент позволяет работать со списком личных задач/заметок",
    "COMPLEX" => "Y",
    "PATH" => [
        "ID" => "my_components",
        "NAME" => "Мои компоненты",
        "CHILD" => [
            "ID" => "personal_tasks_list",
            "NAME" => "Список задач",
        ]
    ],
];
