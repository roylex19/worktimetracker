<template>
    <div class="tracker-container">
        <div class="tracker-items">
            <div class="tracker-item" v-for="(data, index) in trackerData">
                <div class="day" :class="{'holidays': data.day === 'Сб' || data.day === 'Вс'}">
                    <span>{{data.day}}</span>
                </div>
                <div class="hours" :class="{
                    'btn-danger': !data.hours,
                    'btn-success': data.hours,
                }">
                    <span>{{data.hours}}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {mapGetters, mapActions} from 'vuex';

export default {
    computed: {
        ...mapGetters(['trackerData']),
    },
    async mounted(){
        await this.getTrackerData()
    },
    methods: {
        ...mapActions(['getTrackerData']),
    }
}
</script>

<style scoped>
    .tracker-container{
        width: 220px;
        margin: 0;
    }

    .tracker-container .tracker-items{
        margin: 0 auto;
        height: 100%;
        cursor: pointer;
    }

    .tracker-container .tracker-items .tracker-item{
        height: 100%;
        margin: 0 auto;
        display: inline-flex;
        flex-direction: column;
    }

    .tracker-container .tracker-items .tracker-item .day, .tracker-container .tracker-items .tracker-item .hours{
        height: 24px;
        border-radius: 0.25rem;
        padding: 0 5px;
        min-width: 20px;
        margin: auto;
    }

    .holidays{
        color: #dc3545
    }
</style>
