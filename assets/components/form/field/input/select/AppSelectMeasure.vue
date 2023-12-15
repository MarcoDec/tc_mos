<script setup>
    import AppOptionGroups from './AppOptionGroups.vue'

    const props = defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        modelValue: {default: null, type: String}
    })

    function getOptions() {
        if (props.field && props.field.options && props.field.options.options) {
            return props.field.options.options
        }
        return []
    }

    const emit = defineEmits(['update:modelValue'])

    function update(v) {
        emit('update:modelValue', v)
    }

    function input(e) {
        update(e.target.value)
    }
</script>

<template>
    <select
        :id="id"
        :disabled="disabled"
        :form="form"
        :name="field.name"
        :value="modelValue"
        class="form-select form-select-sm"
        @input="input"
        @update:model-value="update">
        <AppOptionGroups v-if="field.hasGroups" :groups="field.groups"/>
        <AppOptions v-else :options="getOptions()"/>
    </select>
</template>
