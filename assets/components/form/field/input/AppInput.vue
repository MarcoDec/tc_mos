<script setup>
    import {computed} from 'vue'

    const props = defineProps({
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String}
    })
    const type = computed(() => props.field.type ?? 'text')
</script>

<template>
    <input
        :id="id"
        :form="form"
        :name="field.name"
        :placeholder="field.label"
        :type="type"
        autocomplete="off"
        class="form-control form-control-sm"/>
</template>
