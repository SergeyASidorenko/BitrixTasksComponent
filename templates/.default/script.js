window.addEventListener('load', function (e) {

    Task = function (el) {
        this.me = el;
        this.task = el.getElementsByClassName('task')[0];
        this.deleteButton = el.getElementsByClassName('delete')[0];
        this.setStatusButton = el.getElementsByClassName('status')[0];
        this.statusInfo = el.getElementsByClassName('status_info')[0];
        this.taskInfo = el.getElementsByClassName("task_info")[0];
        this.taskOptions = el.getElementsByClassName("task_options")[0];
        this.errorNode = document.getElementById('error');
    }
    tasks = document.getElementsByClassName("task_container");
    tasks = Array.from(tasks);
    tasks.forEach(function (elem) {
        var task = new Task(elem);
        elem.addEventListener("click", function (e) {
            let maxHeight = parseInt(getComputedStyle(task.taskInfo).maxHeight);
            if (maxHeight > 0) {
                task.taskInfo.style.maxHeight = "0";
            } else {
                task.taskInfo.style.maxHeight = "300px";
            }
        });
        task.deleteButton.addEventListener('click', function (e) {
            var elementID = task.deleteButton.getAttribute('data_el_id');
            e.preventDefault();
            var popup = BX.PopupWindowManager.create("popup-message", BX('element'), {
                content: 'Удалить задачу?',
                width: 400,
                height: 200,
                zIndex: 100,
                closeIcon: {
                    opacity: 1
                },
                titleBar: 'Удаление задачи ...',
                closeByEsc: true,
                darkMode: true,
                autoHide: true,
                min_height: 100,
                min_width: 100,
                lightShadow: true,
                angle: false,
                overlay: {
                    backgroundColor: 'black',
                    opacity: 500
                },
                buttons: [
                    new BX.PopupWindowButton({
                        text: 'Удалить', // текст кнопки
                        id: 'save-btn', // идентификатор
                        className: 'ui-btn ui-btn-success', // доп. классы
                        events: {
                            click: function () {
                                BX.ajax.runComponentAction('home:tasks',
                                    'deleteTask', {
                                    mode: 'class',
                                    method: 'post',
                                    data: { elementID: elementID },
                                })
                                    .then(function (response) {
                                        if (response.status === 'success') {
                                            task.me.remove();
                                            task.errorNode.classList.remove("visible");
                                            task.errorNode.innerHTML = '';
                                        }
                                        else
                                            task.errorNode.classList.add("visible");
                                        task.errorNode.innerHTML = 'Ошибка при удалении задачи';
                                    });
                                popup.close();
                            }
                        }
                    }),
                    new BX.PopupWindowButton({
                        text: 'Отмена',
                        id: 'copy-btn',
                        className: 'ui-btn ui-btn-primary',
                        events: {
                            click: function () {
                                popup.close();
                            }
                        }
                    })
                ],
            });

            popup.show();
        });
        task.setStatusButton.addEventListener("click", function (e) {
            var elementID = task.setStatusButton.getAttribute('data_el_id');
            var status = task.setStatusButton.getAttribute('data_status');
            e.preventDefault();
            BX.ajax.runComponentAction('home:tasks',
                'setTaskStatus', {
                mode: 'class',
                method: 'post',
                data: { elementID: elementID, status: status },
            })
                .then(function (response) {
                    if (response.status === 'success') {
                        if (status > 0) {
                            task.setStatusButton.setAttribute('data_status', 0);
                            task.setStatusButton.innerHTML = '&#10004;';
                            task.statusInfo.innerHTML = 'Не выполнено';
                            task.task.classList.add("active");

                        } else {
                            task.setStatusButton.setAttribute('data_status', 1);
                            task.setStatusButton.innerHTML = '&#128164;';
                            task.statusInfo.innerHTML = 'Выполнено';
                            task.task.classList.remove("active");
                        }
                        task.errorNode.classList.remove("visible");
                        task.errorNode.innerHTML = '';
                    } else {
                        task.errorNode.classList.add("visible");
                        task.errorNode.innerHTML = 'Ошибка при обновлении задачи';
                    }
                });
        });
        task.taskOptions.addEventListener("click", function (e) { e.stopPropagation(); });
    });
});