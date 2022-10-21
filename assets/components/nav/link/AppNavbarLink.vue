<script setup>
    import {computed} from 'vue'

    const props = defineProps({
        brands: {type: Boolean},
        icon: {required: true, type: String},
        to: {required: true, type: String},
        variant: {default: 'success', type: String}
    })
    const text = computed(() => `text-${props.variant}`)
</script>

<template>
    <AppDropdownItem variant="none">
        <AppRouterLink :css="text" :to="to">
            <Fa :brands="brands" :icon="icon"/>
            <slot/>
        </AppRouterLink>
    </AppDropdownItem>
</template>
