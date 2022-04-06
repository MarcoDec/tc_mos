<script setup>
    import {computed, ref} from 'vue'
    import AppFormGroup from './AppFormGroup.vue'

    const form = ref()
    const emit = defineEmits(['submit', 'update:modelValue'])
    const props = defineProps({
        fields: {default: () => [], type: Array},
        id: {required: true, type: String},
        inline: {required: false, type: Boolean},
        modelValue: {default: () => ({}), type: Object}
    })
    const displayInline = computed(() => ({'d-inline': props.inline, 'm-0': props.inline, 'p-0': props.inline}))

    function input(value) {
        emit('update:modelValue', {...props.modelValue, [value.name]: value.value})
    }

    function submit() {
        if (typeof form.value !== 'undefined')
            emit('submit', new FormData(form.value))
    }
</script>

<template>
    <form :id="id" ref="form" :class="displayInline" autocomplete="off" @submit.prevent="submit">
        <slot v-if="inline"/>
        <template v-else>
            <AppFormGroup
                v-for="field in fields"
                :key="field.name"
                :field="field"
                :form="id"
                :model-value="modelValue[field.name]"
                @input="input"/>
            <div class="float-start">
                <slot name="start"/>
            </div>
            <div class="float-end">
                <slot/>
            </div>
        </template>
    </form>
</template>
