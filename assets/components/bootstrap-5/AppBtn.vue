<script setup>
    import {computed} from 'vue'

    const props = defineProps({
        icon: {default: null, type: String},
        size: {default: 'sm', type: String},
        type: {default: 'button', type: String},
        variant: {default: 'primary', type: String}
    })
    const btnClass = computed(() => ({
        [`btn-${props.size} btn-${props.variant}`]: true,
        'btn-icon': Boolean(props.icon)
    }))
</script>

<template>
    <button :class="btnClass" :type="type" class="btn">
        <slot>
            <Fa v-if="icon" :icon="icon"/>
        </slot>
    </button>
</template>
