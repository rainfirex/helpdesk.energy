<template>
</template>

<script>
    export default {
        name: "Notification",
        data() {
            return {
                permission: null,
                message: ''
            }
        },
        mounted() {

            if (!("Notification" in window)) {
                alert('Ваш браузер не поддерживает HTML Notifications, его необходимо обновить.');
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
                this.permission = permission;
                this.message = message;

               console.dir(message);

                var notification = new Notification("TEST TEST TEST!", {
                    body : 'Привет, это тестовое уведомление!!!'
                });
            });
        }
    }
</script>
