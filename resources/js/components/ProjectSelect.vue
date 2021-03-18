<template>
    <v-select
        label="title"
        placeholder="Выберите значение из списка"
        :options="options"
        @search="onSearch"
        :filterable="false"
        v-model="inputVal"
    />
</template>

<script>

import 'vue-select/dist/vue-select.css'
import vSelect from 'vue-select'
import {mapGetters, mapActions} from 'vuex'
const _ = require('lodash')

export default {
    components: {
        vSelect,
    },
    props: {
        value: {
            type: Object
        },
        records: {
            type: Array
        }
    },
    computed: {
        inputVal: {
            get() {
                return this.value;
            },
            set(val) {
                this.$emit('input', val);
            }
        }
    },
    data(){
        return{
            options: []
        }
    },
    mounted() {
        this.setRecords()
    },
    methods: {
        onSearch(search, loading){
            if(search.length){
                loading(true)
                this.search(loading, search, this)
            }
            else{
                this.setRecords()
            }
        },
        search: _.debounce((loading, search, vm) => {
            axios.get('/api/projects/' + search)
                .then(response => {
                    vm.options = response.data
                    loading(false)
                })
                .catch((e) => {
                    Vue.$toast.open({
                        message: e.name + ": " + e.message,
                        type: 'error',
                    })
                })
        }, 350),
        setRecords(){
            this.options = this.records
        }
    }
}
</script>

<style scoped>

</style>
