<template>
    <div class="modal fade" :id="id" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Подтверждение</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>{{message}}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" @click="CloseWindow">Да</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'confirm-window',
    props: {
        id: {
            type: String,
            required: false,
            default: 'exampleModal'
        },
        message:{
            type: String,
            required: false,
            default: 'Уверены, что хотите создать запись?'
        },
    },
    methods: {
        CloseWindow(){
            this.$emit('onClose')
        }
    }
}
</script>

<style scoped>

</style>
