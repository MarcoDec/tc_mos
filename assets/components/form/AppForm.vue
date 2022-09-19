<script setup>
    import AppFormGroup from './field/AppFormGroup.vue'

    defineProps({fields: {required: true, type: Array}, id: {required: true, type: String}})
</script>

<template>
    <form :id="id" autocomplete="off" enctype="multipart/form-data" method="post" novalidate>
        <AppFormGroup v-for="field in fields" :key="field.name" :field="field" :form="id"/>
    </form>
</template>
