export default {
    init() {
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
            store.dispatch('initPermission', { permission, message });
        });
    },

    play(title, text, icon = '') {

        const options = {
            body: text,
            icon: icon,
            dir: 'auto'
        };

        // Проверка прав
        if (Notification.permission === "granted") {

            const notification = new Notification(title, options); // Отправляем уведомление

        } else if (Notification.permission === 'default') { // Если прав нет, пытаемся их получить

            Notification.requestPermission(function (permission) {

                // Если права успешно получены, отправляем уведомление

                if (permission === "granted") {
                    store.dispatch('initPermission', {permission, message: 'Пользователь разрешил показывать уведомления'});
                    const notification = new Notification(title, options);
                }
            });
        }


    }
}
