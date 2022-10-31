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
        <slot>
            <Fa v-if="icon" :icon="icon"/>
            <template v-else>
                {{ label }}
            </template>
        </slot>
    </button>
</template>
