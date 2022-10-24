<script setup>
    import AppOptionGroups from './AppOptionGroups.vue'

    defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        modelValue: {default: null, type: String}
    })
    const emit = defineEmits(['update:modelValue'])

    function update(v) {
        emit('update:modelValue', v)
    }

    function input(e) {
        update(e.target.value)
    }
</script>

<template>
    <AppMultiselect
        v-if="field.big"
        :id="id"
        :disabled="disabled"
        :field="field"
        :form="form"
        :value="modelValue"
        mode="single"
        @update:model-value="update"/>
    <select
        v-else
        :id="id"
        :disabled="disabled"
        :form="form"
        :name="field.name"
        :value="modelValue"
        class="form-select form-select-sm"
        @input="input">
        <AppOptionGroups v-if="field.hasGroups" :groups="field.groups"/>
        <AppOptions v-else :options="field.optionsList"/>
    </select>
</template>
