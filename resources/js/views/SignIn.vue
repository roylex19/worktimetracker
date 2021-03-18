<template>
    <div class="container">
        <div class="row justify-content-center">
            <form @submit.prevent="submit">
                <h5 class="mb-3">Авторизация</h5>
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
                    <label>Пароль</label>
                    <input type="password" class="form-control" v-model.trim="form.password" :class="{'is-invalid':
                    ($v.form.password.$dirty && !$v.form.password.required) ||
                    ($v.form.password.$dirty && !$v.form.password.minLength) }">
                    <div class="invalid-feedback" v-if="$v.form.password.$dirty">
                        Длина пароля минимум 6 символов
                    </div>
                </div>
                <div class="mb-3">
                    Еще не зарегистрированы?
                    <router-link href="#" :to="{name: 'register'}" class="text-decoration-none">Зарегистрироваться</router-link>
                </div>
                <button type="submit" class="btn btn-primary" :disabled="load">Войти</button>
            </form>
        </div>
    </div>
</template>

<script>
import {mapActions} from 'vuex'
import {numeric, required, minLength, maxLength, helpers} from 'vuelidate/lib/validators'
const phone = helpers.regex('phone', /^\+7\d{10}$/)
export default {
    data(){
        return{
            load: false,
            form: {
                phone: '',
                password: '',
            }
        }
    },
    validations: {
        form: {
            phone: {
                phone,
                required,
            },
            password: {
                required,
                minLength: minLength(6),
            }
        }
    },
    methods: {
        ...mapActions({
            signIn: 'auth/signIn'
        }),
        submit(){
            if(this.$v.$invalid){
                this.$v.$touch()
                return
            }

            this.load = true;
            this.signIn(this.form).then(() => {
                this.load = false
                this.$router.replace({
                    name: 'myRecords'
                })
            })
        }
    }
}
</script>

<style scoped>
    form{
        width: 353px;
    }
    .show-pass-eye{
        position: absolute;

    }
</style>
