<template>
    <div>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <router-link :to="{name: 'home'}" class="navbar-brand">
                <img src="/logo_new.png" height="40" alt="На главную" v-if="isNewYear">
                <img src="/logo.png" height="40" alt="На главную" v-else>
            </router-link>
            <button v-if="auth" class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent" v-if="auth">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item" v-if="user.admin">
                        <router-link :to="{name: 'projects'}" class="nav-link">Проекты</router-link>
                    </li>
                    <li class="nav-item" v-if="user.admin">
                        <router-link :to="{name: 'users'}" class="nav-link">Сотрудники</router-link>
                    </li>
                    <li class="nav-item" v-if="user.admin">
                        <router-link :to="{name: 'departments'}" class="nav-link">Отделы</router-link>
                    </li>
                    <li class="nav-item" v-if="user.admin">
                        <router-link :to="{name: 'records'}" class="nav-link">Общий список отчетов</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link :to="{name: 'myRecords'}" class="nav-link">Отчеты</router-link>
                    </li>
                </ul>

                <div class="mr-4 float-right" v-if="isDecember" style="font-size: 12px">
                    <span>До нового года {{newYear.year}}</span><br>
                    <!--<span>До корпоратива {{newYear.corp}}</span>-->
                </div>

                <form class="form-inline my-2 my-lg-0">
                    <a href="logout" class="my-2 my-sm-0" @click.prevent="signOut"><i class="fas fa-sign-out-alt"></i> Выйти</a>
                </form>
            </div>
        </nav>

        <update-info v-if="auth"></update-info>

        <main class="py-4">
            <router-view></router-view>
            <vue-progress-bar></vue-progress-bar>
        </main>
    </div>
</template>

<script>
    import {mapGetters, mapActions, mapState} from "vuex";
    import UpdateInfo from "../components/UpdateInfo";

    export default {
        components: {
            UpdateInfo,
        },
        data(){
            return{
                newYear: {
                    corp: null,
                    year: null,
                    corpDay: 26,
                },
            }
        },
        computed: {
            ...mapGetters({
                auth: 'auth/auth',
                user: 'auth/user',
            }),
            isNewYear(){
                return moment().month() === 11 || (moment().month() === 0 && moment().date() < 20)
            },
            isDecember(){
                return moment().month() === 11
            }
        },
        async mounted() {
            if(this.isNewYear) {
                moment.locale('ru')
                this.getDays()
                this.timeUpdate()
            }

            this.$Progress.finish()
        },
        created () {
            this.$Progress.start()
            this.$router.beforeEach((to, from, next) => {
                if (to.meta.progress !== undefined) {
                    let meta = to.meta.progress
                    this.$Progress.parseMeta(meta)
                }
                this.$Progress.start()
                next()
            })
            this.$router.afterEach((to, from) => {
                this.$Progress.finish()
            })
        },
        methods: {
            ...mapActions({
                signOutAction: 'auth/signOut'
            }),
            signOut(){
                this.signOutAction().then(() => {
                    this.$router.replace({
                        name: 'signIn'
                    })
                })
            },
            timeUpdate(){
                setInterval(() => {
                    this.getDays()
                }, 3600)
            },
            getDays(){
                const currYear = moment().year()
                this.newYear.year = moment([currYear + 1, 0, 1]).fromNow(true)
                //this.newYear.corp = moment([currYear, moment().month(), this.newYear.corpDay]).fromNow(true)
            }
        }
    }
</script>
