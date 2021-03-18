<template>
    <div class="alert alert-primary alert-dismissible fade show" role="alert" v-if="!showUpdateInfo">
        <strong>Добавлена сортировка записей в таблицах!</strong>
        Теперь при нажатии на заголовок столбца в таблице производится сортировка.
        <ul>
            <li>При первом нажатии - по возрастанию</li>
            <li>При втором нажатии - по убыванию</li>
            <li>При третьем нажатии - сбрасывается</li>
        </ul>
        Сортировку можно производить по нескольким столбцам, а также комбинировать с фильтрацией.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close" @click="showUpdateInfo = null">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</template>

<script>
export default {
    computed: {
        showUpdateInfo: {
            get(){
                return !!localStorage.getItem('showUpdateInfo')
            },
            set(val){
                localStorage.setItem('showUpdateInfo', val)
            }
        }
    },
}
</script>

<style scoped>
    .alert{
        margin-bottom: 0;
    }
</style>
