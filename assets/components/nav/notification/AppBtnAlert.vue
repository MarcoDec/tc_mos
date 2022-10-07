<script setup>
    import {computed} from 'vue'

    const props = defineProps({
        disabled: {type: Boolean},
        icon: {required: true, type: String},
        variant: {default: 'danger', type: String}
    })
    const bg = computed(() => `bg-${props.disabled ? 'dark' : props.variant}`)
</script>

<template>
    <AppBtn :disabled="disabled" :icon="icon" class="position-relative" label="Notifications" variant="secondary">
        <span :class="bg" class="badge position-absolute rounded-pill start-100 top-0 translate-middle">
            <slot/>
        </span>
    </AppBtn>
</template>
