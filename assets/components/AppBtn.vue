<script setup>
    import {computed} from 'vue'

    const props = defineProps({
        disabled: {type: Boolean},
        icon: {default: null, type: String},
        label: {required: true, type: String},
        type: {default: 'button', type: String},
        variant: {default: 'primary', type: String}
    })
    const css = computed(() => `btn-${props.variant} btn-${props.icon ? 'icon' : 'sm'}`)
</script>

<template>
    <button :class="css" :disabled="disabled" :title="label" :type="type" class="btn">
        <Fa v-if="icon" :icon="icon"/>
        <slot>
            <template v-if="!icon">
                {{ label }}
            </template>
        </slot>
    </button>
</template>
