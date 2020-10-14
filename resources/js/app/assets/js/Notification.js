export default {
    initPermission() {
        if (!("Notification" in window)) {
            store.dispatch('initPermission', {
                permission: false,
                message: 'Ваш браузер не поддерживает HTML Notifications, его необходимо обновить.'
            });
        }

        Notification.requestPermission(permission => {
            let message;
            switch (permission) {
                case 'default ':
                    message = 'Запрос на получение прав не отправлялся';
                    break;
                case "denied":
                    message = 'Пользователь запретил показывать уведомления';
                    break;
                case "granted":
                    message = 'Пользователь разрешил показывать уведомления';
                    break;
            }
            store.dispatch('initPermission', {permission, message});
        });
    },

    play(title, text) {


        // Проверка прав
        if (Notification.permission === "granted") {
// Отправляем уведомление
            var notification = new Notification(title, options);
        } else if (Notification.permission === 'default') {
// Если прав нет, пытаемся их получить
            Notification.requestPermission(function(permission) {
// Если права успешно получены, отправляем уведомление
                if (permission === "granted") {
                    var notification = new Notification(title, options);
                }
            });
        }

        // const options = {
        //     body: text,
        //     icon: '',
        //     dir: 'auto'
        // };


        // let notification = new Notification(title, options);

        // console.dir(Notification);
        //
        // if (Notification.permission === "granted") {
        //
        //
        //
        //
        //
        //
        //     let notification = new Notification(title, options);
        // } else if (Notification.permission !== 'denied') {
        //
        //
        //     Notification.requestPermission(function (permission) {
        //         // Если пользователь разрешил, то создаем уведомление
        //         if (permission === "granted") {
        //             let notification = new Notification(title, options);
        //         }
        //     });
        // }
        //
        // console.dir(notification)
    }
}
