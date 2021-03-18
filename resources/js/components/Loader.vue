<template>
    <div class="d-flex justify-content-center">
        <div class="spinner-border spin-size" :class="color" role="status"/>
    </div>
</template>

<script>
    export default {
        computed: {
            color(){
                const colors = [
                    'text-primary',
                    'text-secondary',
                    'text-success',
                    'text-danger',
                    'text-warning',
                    'text-info',
                ]
                return colors[Math.floor(Math.random() * colors.length - 1)]
            }
        }
    }
</script>

<style scoped>
    .spin-size{
        width: 3rem;
        height: 3rem;
    }
</style>
