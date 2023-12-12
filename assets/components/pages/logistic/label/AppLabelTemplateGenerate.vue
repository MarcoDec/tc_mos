<script setup>
    import {useRoute} from 'vue-router'
    const route = useRoute()
    const id_label_template = route.params.id_label_template
    console.log('id_label_template', id_label_template)
</script>

<template>
    HELLO WORLD {{ id_label_template }}
</template>

<style scoped>

</style>