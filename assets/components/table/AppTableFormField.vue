<script setup>
    import {computed, onMounted, onUnmounted, ref, shallowRef} from 'vue'
    import {Tooltip} from 'bootstrap'

    const el = ref()
    const props = defineProps({
        field: {required: true, type: Object},
        form: {required: true, type: String},
        store: {required: true, type: Object}
    })
    const tip = '<i class="enter-key-icon"></i> pour rechercher'
    const tooltip = shallowRef(null)
    const input = computed(() => `${props.form}-${props.field.name}`)

    function dispose() {
        if (tooltip.value !== null) {
            tooltip.value.dispose()
            tooltip.value = null
        }
    }

    function instantiate() {
        if (typeof el.value === 'undefined')
            return
        dispose()
        tooltip.value = new Tooltip(el.value)
    }

    onMounted(instantiate)
    onUnmounted(dispose)
</script>

<template>
    <td>
        <AppInputGuesser
            :id="input"
            ref="el"
            v-model="store.search[field.name]"
            :field="field"
            :form="form"
            :title="tip"
            data-bs-html="true"
            data-bs-placement="top"
            data-bs-toogle="tooltip"/>
    </td>
</template>
