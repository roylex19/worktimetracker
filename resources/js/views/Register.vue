<template>
    <div class="container">
        <div class="row justify-content-center">
            <form @submit.prevent="submit">
                <h5 class="mb-3">Регистрация</h5>
                <div class="form-group">
                    <label>Номер телефона</label>
                        <input type="text" class="form-control" v-model.trim="form.phone" :class="{'is-invalid':
                        ($v.form.phone.$dirty && !$v.form.phone.phone) ||
                        ($v.form.phone.$dirty && !$v.form.phone.required)}">
                    <div class="invalid-feedback" v-if="$v.form.phone.$dirty">
                        Введите корректный номер телефона
                    </div>
                </div>
                <div class="form-group">
                    <label>Фамилия</label>
                    <input type="text" class="form-control" v-model.trim="form.lastname" :class="{'is-invalid':
                    ($v.form.lastname.$dirty && !$v.form.lastname.required) ||
                    ($v.form.lastname.$dirty && !$v.form.lastname.alpha) ||
                    ($v.form.lastname.$dirty && !$v.form.lastname.minLength)}">
                    <div class="invalid-feedback" v-if="$v.form.lastname.$dirty">
                        Введите фамилию
                    </div>
                </div>
                <div class="form-group">
                    <label>Имя</label>
                    <input type="text" class="form-control" v-model.trim="form.firstname" :class="{'is-invalid':
                    ($v.form.firstname.$dirty && !$v.form.firstname.required) ||
                    ($v.form.firstname.$dirty && !$v.form.firstname.alpha) ||
                    ($v.form.firstname.$dirty && !$v.form.firstname.minLength)}">
                    <div class="invalid-feedback" v-if="$v.form.firstname.$dirty">
                        Введите имя
                    </div>
                </div>
                <div class="form-group">
                    <label>Отчество</label>
                    <input type="text" class="form-control" v-model.trim="form.middlename" :class="{'is-invalid':
                    ($v.form.middlename.$dirty && !$v.form.middlename.required) ||
                    ($v.form.middlename.$dirty && !$v.form.middlename.alpha) ||
                    ($v.form.middlename.$dirty && !$v.form.middlename.minLength)}">
                    <div class="invalid-feedback" v-if="$v.form.middlename.$dirty">
                        Введите отчество
                    </div>
                </div>
                <div class="form-group">
                    <label>Отдел</label>
                    <v-select placeholder="Выберите отдел"
                              :options="departmentsList"
                              label="title"
                              v-model="form.department" :class="{'is-invalid':
                              ($v.form.department.$dirty && !$v.form.department.required)}">
                    </v-select>
                    <div class="invalid-feedback" v-if="$v.form.department.$dirty">
                        Выберите отдел
                    </div>
                </div>
                <div class="form-group">
                    <label>Пароль</label>
                    <input type="password" class="form-control" v-model.trim="form.password" :class="{'is-invalid':
                    ($v.form.password.$dirty && !$v.form.password.required) ||
                    ($v.form.password.$dirty && !$v.form.password.minLength)}">
                    <div class="invalid-feedback" v-if="$v.form.password.$dirty">
                        Длина пароля минимум 6 символов
                    </div>
                </div>
                <div class="form-group">
                    <label>Подтверждение пароля</label>
                    <input type="password" class="form-control" v-model.trim="form.repeatPassword" :class="{'is-invalid':
                    ($v.form.repeatPassword.$dirty && !$v.form.repeatPassword.sameAsPassword)}">
                    <div class="invalid-feedback" v-if="$v.form.repeatPassword.$dirty">
                        Пароли не совпадают
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" :disabled="load">Регистрация</button>
            </form>
        </div>
    </div>
</template>

<script>
import vSelect from 'vue-select'
import {mapActions, mapGetters} from 'vuex'
import {maxLength, minLength, numeric, required, sameAs, helpers} from "vuelidate/lib/validators"
const alpha = helpers.regex('alpha', /^[А-яёЁ]*$/)
const phone = helpers.regex('phone', /^\+7\d{10}$/)
export default {
    components: {
        vSelect
    },
    data(){
        return{
            loading: true,
            load: false,
            form: {
                phone: '',
                firstname: '',
                lastname: '',
                middlename: '',
                department: null,
                password: '',
                repeatPassword: ''
            },
        }
    },
    validations: {
        form: {
            phone: {
                phone,
                required,
            },
            firstname: {
                required,
                minLength: minLength(2),
                alpha
            },
            lastname: {
                required,
                minLength: minLength(2),
                alpha
            },
            middlename: {
                required,
                minLength: minLength(2),
                alpha
            },
            department: {
                required
            },
            password: {
                required,
                minLength: minLength(6),
            },
            repeatPassword: {
                sameAsPassword: sameAs('password')
            }
        }
    },
    computed: {
        ...mapGetters(['departmentsList'])
    },
    async mounted() {
        await this.getDepartmentsList()

        this.loading = false
    },
    methods: {
        ...mapActions(['getDepartments', 'getDepartmentsList']),
        ...mapActions({
            register: 'auth/register'
        }),
        submit() {
            if(this.$v.$invalid){
                this.$v.$touch()
                return
            }

            this.load = true
            this.register(this.form).then(() => {
                this.load = false
                this.$router.replace({
                    name: 'signIn'
                })
            })
        }
    }
}
</script>

<style scoped>
    form{
        min-width: 294px;
    }
</style>
