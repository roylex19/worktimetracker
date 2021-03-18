<template>
    <loader v-if="loading"/>

    <div v-else>
        <p>Норма за месяц: {{userHours.dueHours}} ч.</p>
        <p>Всего отработано: {{userHours.totalHours}} ч.</p>
        <p>Переработано: {{userHours.overtime}} ч.</p>
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex'
import Loader from "../components/Loader";

export default {
    components: {
        Loader
    },
    computed: {
        ...mapGetters(['userHours']),
        ...mapGetters({
            auth: 'auth/auth',
            user: 'auth/user',
        }),
    },
    data(){
        return{
            loading: true
        }
    },
    async mounted(){
        await this.getUserHours({
            userId: this.user.id,
            month: 1,
            year: 2021
        })

        this.loading = false
    },
    methods: {
        ...mapActions(['getUserHours'])
    }
}
</script>

<style scoped>

</style>
