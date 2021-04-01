<template>
    <div class="content" ref="content">
        <h2 class="text-center">Заявка на подключение к информационным ресурсам</h2>
        <hr>
        <div>
            <!--Информация о сотруднике-->
            <div class="mb-5">
                <h4 class="text-center mb-3">Информация о сотруднике</h4>
                <!--Сотрудник-->
                <div class="row form-group">
                    <div class="offset-md-1 col-md-4 col-form-label">
                        <label>Сотрудник</label>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control"
                                   :class="{'error-input': $v.user.name.$error}"
                                   @change="$v.user.name.$touch"
                                   v-model="user.name" placeholder="" :disabled="isLoading" required>
                        </div>
                        <small class="form-text text-muted" :class="{'is-error': $v.user.name.$error}">
                                    <span v-if="!$v.user.name.required" class="error-text"
                                          :class="{'error-show': !$v.user.name.required}">Поле пустое</span>
                            <span v-if="!$v.user.name.minLength" class="error-text"
                                  :class="{'error-show': !$v.user.name.minLength}">Минимум {{$v.user.name.$params.minLength.min}} символа</span>
                        </small>
                    </div>
                </div>
                <!--Руководитель подразделения-->
                <div class="row form-group">
                    <div class="offset-md-1 col-md-4 col-form-label">
                        <label>Руководитель подразделения</label>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control"
                                   :class="{'error-input': $v.user.leader.$error}"
                                   @change="$v.user.leader.$touch"
                                   v-model="user.leader" placeholder="" :disabled="isLoading" required>
                        </div>
                        <small class="form-text text-muted" :class="{'is-error': $v.user.leader.$error}">
                                    <span v-if="!$v.user.leader.required" class="error-text"
                                          :class="{'error-show': !$v.user.leader.required}">Поле пустое</span>
                            <span v-if="!$v.user.leader.minLength" class="error-text"
                                  :class="{'error-show': !$v.user.leader.minLength}">Минимум {{$v.user.leader.$params.minLength.min}} символа</span>
                        </small>
                    </div>
                </div>
                <!--Должность-->
                <div class="row form-group">
                    <div class="offset-md-1 col-md-4 col-form-label">
                        <label>Должность</label>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control"
                                   :class="{'error-input': $v.user.function.$error}"
                                   @change="$v.user.function.$touch"
                                   v-model="user.function" placeholder="" :disabled="isLoading" required>
                        </div>
                        <small class="form-text text-muted" :class="{'is-error': $v.user.function.$error}">
                                    <span v-if="!$v.user.function.required" class="error-text"
                                          :class="{'error-show': !$v.user.function.required}">Поле пустое</span>
                            <span v-if="!$v.user.function.minLength" class="error-text"
                                  :class="{'error-show': !$v.user.function.minLength}">Минимум {{$v.user.function.$params.minLength.min}} символа</span>
                        </small>
                    </div>
                </div>
                <!--Подразделение-->
                <div class="row form-group">
                    <div class="offset-md-1 col-md-4 col-form-label">
                        <label>Подразделение</label>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control"
                                   :class="{'error-input': $v.user.unit.$error}"
                                   @change="$v.user.unit.$touch"
                                   v-model="user.unit" placeholder="" :disabled="isLoading" required>
                        </div>
                        <small class="form-text text-muted" :class="{'is-error': $v.user.unit.$error}">
                                    <span v-if="!$v.user.unit.required" class="error-text"
                                          :class="{'error-show': !$v.user.unit.required}">Поле пустое</span>
                            <span v-if="!$v.user.unit.minLength" class="error-text"
                                  :class="{'error-show': !$v.user.unit.minLength}">Минимум {{$v.user.unit.$params.minLength.min}} символа</span>
                        </small>
                    </div>
                </div>
                <!--Адрес (место работы)-->
                <div class="row form-group">
                    <div class="offset-md-1 col-md-4 col-form-label">
                        <label>Адрес (место работы)</label>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control"
                                   :class="{'error-input': $v.user.address.$error}"
                                   @change="$v.user.address.$touch"
                                   v-model="user.address" placeholder="" :disabled="isLoading" required>
                        </div>
                        <small class="form-text text-muted" :class="{'is-error': $v.user.address.$error}">
                                    <span v-if="!$v.user.address.required" class="error-text"
                                          :class="{'error-show': !$v.user.address.required}">Поле пустое</span>
                            <span v-if="!$v.user.address.minLength" class="error-text"
                                  :class="{'error-show': !$v.user.address.minLength}">Минимум {{$v.user.address.$params.minLength.min}} символа</span>
                        </small>
                    </div>
                </div>
                <!--Кабинет-->
                <div class="row form-group">
                    <div class="offset-md-1 col-md-4 col-form-label">
                        <label>Кабинет</label>
                    </div>
                    <div class="offset-md-3 col-md-3">
                        <div class="input-group">
                            <input type="text" class="form-control"
                                   :class="{'error-input': $v.user.cabinet.$error}"
                                   @change="$v.user.cabinet.$touch"
                                   v-model="user.cabinet" placeholder="" :disabled="isLoading" required>
                        </div>
                        <small class="form-text text-muted" :class="{'is-error': $v.user.cabinet.$error}">
                                    <span v-if="!$v.user.cabinet.required" class="error-text"
                                          :class="{'error-show': !$v.user.cabinet.required}">Поле пустое</span>
                            <span v-if="!$v.user.cabinet.minLength" class="error-text"
                                  :class="{'error-show': !$v.user.cabinet.minLength}">Минимум {{$v.user.cabinet.$params.minLength.min}} символа</span>
                        </small>
                    </div>
                </div>
                <!--Телефон-->
                <div class="row form-group">
                    <div class="offset-md-1 col-md-4 col-form-label">
                        <label>Телефон</label>
                    </div>
                    <div class="offset-md-3 col-md-3">
                        <div class="input-group">
                            <input type="tel" class="form-control"
                                   :class="{'error-input': $v.user.phone.$error}"
                                   @change="$v.user.phone.$touch"
                                   v-model="user.phone" placeholder="" :disabled="isLoading" required>
                        </div>
                        <small class="form-text text-muted" :class="{'is-error': $v.user.phone.$error}">
                                    <span v-if="!$v.user.phone.required" class="error-text"
                                          :class="{'error-show': !$v.user.phone.required}">Поле пустое</span>
                            <span v-if="!$v.user.phone.numeric" class="error-text"
                                  :class="{'error-show': !$v.user.phone.numeric}">Допустимы только Цыфры</span>
                            <span v-if="!$v.user.phone.minLength" class="error-text"
                                  :class="{'error-show': !$v.user.phone.minLength}">Минимум {{$v.user.phone.$params.minLength.min}} символа</span>
                        </small>
                    </div>
                </div>
                <!--Период-->
                <div class="row form-group">
                    <div class="offset-md-1 col-md-2 col-form-label">
                        <label>Период</label>
                    </div>
                    <div class="offset-md-2 col-md-1 col-form-label text-right">
                        <label>С</label>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <input type="date" class="form-control"
                                   :class="{'error-input': $v.user.perStart.$error}"
                                   @change="$v.user.perStart.$touch"
                                   v-model="user.perStart" placeholder="" :disabled="isLoading" required>
                        </div>
                        <small class="form-text text-muted" :class="{'is-error': $v.user.perStart.$error}">
                                    <span v-if="!$v.user.perStart.required" class="error-text"
                                          :class="{'error-show': !$v.user.perStart.required}">Поле пустое</span>
                        </small>
                    </div>
                    <div class="col-md-1 col-form-label text-right">
                        <label>По</label>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <input type="date" class="form-control"
                                   :class="{'error-input': $v.user.perEnd.$error}"
                                   @change="$v.user.perEnd.$touch"
                                   v-model="user.perEnd" placeholder="" :disabled="isLoading">
                        </div>
                        <small class="form-text text-muted" :class="{'is-error': $v.user.perEnd.$error}">
                                    <span v-if="!$v.user.perEnd.required" class="error-text"
                                          :class="{'error-show': !$v.user.perEnd.required}">Поле пустое</span>
                        </small>
                    </div>
                </div>
            </div>
            <!--Информационные ресурсы-->
            <div class="mb-5">
                <h4 class="text-center mb-3">Доступ к информационным ресурсам</h4>
                <div class="row form-group box-check d-md-flex flex-md-wrap justify-content-md-center">
                    <div class="block">
                        <!--Доступ к ресурсам ЛВС (учетная запись)-->
                        <div class="form-check">
                            <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" v-model="user.access.isLogin">
                                Доступ к ресурсам ЛВС (учетная запись)
                            </label>
                        </div>
                        <!--1С УПП-->
                        <div class="form-check">
                            <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" v-model="user.access.is1CUPP">
                                1С УПП
                            </label>
                        </div>
                        <!--1С Зарплата и Управление персоналом-->
                        <div class="form-check">
                            <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" v-model="user.access.is1CZPP">
                                1С Зарплата и Управление персоналом
                            </label>
                        </div>
                        <!--АСУСЭ-->
                        <div class="form-check">
                            <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" v-model="user.access.isAsuse">
                                АСУСЭ
                            </label>
                        </div>
                        <!--ОМНИУС-->
                        <div class="form-check">
                            <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" v-model="user.access.isOmnius">
                                ОМНИУС
                            </label>
                        </div>
                        <!--USB-->
                        <div class="form-check">
                            <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" v-model="user.access.isUSB">
                                USB
                            </label>
                        </div>
                    </div>
                    <div class="ml-md-4 block">
                        <!--Электронная почта-->
                        <div class="form-check">
                            <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" v-model="user.access.isEmail">
                                Электронная почта
                            </label>
                        </div>
                        <!--Интернет-->
                        <div class="form-check">
                            <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" v-model="user.access.isInternet">
                                Интернет
                            </label>
                        </div>
                        <!--Консультант плюс-->
                        <div class="form-check">
                            <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" v-model="user.access.isConsult">
                                Консультант плюс
                            </label>
                        </div>
                        <!--Папка обмен-->
                        <div class="form-check">
                            <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" v-model="user.access.isFolderObmen">
                                Папка обмен
                            </label>
                        </div>
                        <!--Work от УТД-->
                        <div class="form-check">
                            <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" v-model="user.access.isWorkFromUTD">
                                Work от УТД
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <!--Доступ к сетевым ресурсам-->
            <div class="mb-5">
                <h4 class="text-center mb-3">Доступ к сетевым ресурсам</h4>
                <div class="form-group offset-md-1 col-md-10">
                    <textarea class="form-control" rows="3" v-model="user.access.lanResource"></textarea>
                </div>
            </div>
            <!--Другие программные продукты-->
            <div class="mb-5">
                <h4 class="text-center mb-3">Другие программные продукты</h4>
                <div class="form-group offset-md-1 col-md-10">
                    <textarea class="form-control" rows="3" v-model="user.access.otherProgram"></textarea>
                </div>
            </div>
        </div>

        <div class="text-center">
            <button class="btn btn-info" @click="create">Создать</button>
        </div>
    </div>
</template>

<script>
    import {required, minLength, numeric} from 'vuelidate/lib/validators';
    import {mapGetters, mapActions} from 'vuex';
    import Sound from "../../assets/js/Sound";
    export default {
        name: "RequestInfoResource",
        data() {
            return {
                isLoading: false,
                user: {
                    name: '',
                    leader: '',
                    function: '',
                    unit: '',
                    address: '',
                    cabinet: '',
                    phone: '',
                    perStart: '',
                    perEnd: '',
                    access: {
                        isLogin: false,
                        is1CUPP: false,
                        is1CZPP: false,
                        isAsuse: false,

                        isOmnius: false,
                        isUSB: false,
                        isFolderObmen: false,
                        isWorkFromUTD: false,

                        isEmail: false,
                        isInternet: false,
                        isConsult: false,
                        lanResource: '',
                        otherProgram: ''
                    }
                },
            }
        },
        validations: {
            user: {
                name: {
                    required,
                    minLength: minLength(4)
                },
                leader: {
                    required,
                    minLength: minLength(4)
                },
                function: {
                    required,
                    minLength: minLength(4)
                },
                unit: {
                    required,
                    minLength: minLength(2)
                },
                address: {
                    required,
                    minLength: minLength(6)
                },
                cabinet: {
                    required,
                    minLength: minLength(1)
                },
                phone: {
                    required,
                    numeric,
                    minLength: minLength(6)
                },
                perStart: {
                    required
                },
                perEnd: {
                    required
                }
            }
        },
        methods: {
            ...mapActions(['setMessenger', 'setLoaderBar']),
            create() {
                this.$v.$touch();

                if (this.$v.$invalid) {
                    this.setMessenger({text: 'Заполните все поля!', status: 'error'});
                    return false;
                }

                this.isLoading = false;
                this.setLoaderBar(true);

                const frmData = new FormData();
                frmData.append('user', JSON.stringify(this.user));
                frmData.append('html', this.$refs.content.innerHTML);


                axios.post(`/api/resource-access/create`, frmData)
                    .then(response => {
                        if (response.data.success) {
                            this.defaultUser();
                            this.$v.$reset();
                            this.setMessenger({text: 'Заявка на доступ к информационным ресурсам создана.', status: 'success'});
                            Sound.playSound('/sounds/_notify.mp3');
                        }
                    })
                    .catch(error => {
                        Sound.playSound('/sounds/_alert.mp3');
                        this.errors = error.response.data.message;
                        this.setMessenger({text: this.errors, status: 'error'});
                    })
                    .finally(() => {
                        this.isLoading = false;
                        this.setLoaderBar(false);
                    });
            },
            defaultUser(){
                this.user = {
                    name: '',
                    leader: '',
                    function: '',
                    unit: '',
                    address: '',
                    cabinet: '',
                    phone: '',
                    perStart: '',
                    perEnd: '',
                    access: {
                        isLogin: false,
                        is1CUPP: false,
                        is1CZPP: false,
                        isAsuse: false,

                        isOmnius: false,
                        isUSB: false,
                        isFolderObmen: false,
                        isWorkFromUTD: false,

                        isEmail: false,
                        isInternet: false,
                        isConsult: false,
                        lanResource: '',
                        otherProgram: ''
                    }
                };
            }
        }
    }
</script>

<style lang="scss" scoped>
    .box-check {
        padding: 0 15px 15px;
        /*border: solid 1px #dedede;*/
        margin: 5px;

        .block {
            .form-check-label {
                margin: 5px 0;
                user-select: none;
            }
        }
    }
</style>