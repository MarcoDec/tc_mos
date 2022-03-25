<script lang="ts" setup>
    import {computed, defineProps} from 'vue'

    const props = defineProps({
        brands: {required: false, type: Boolean},
        disabled: {required: false, type: Boolean},
        icon: {required: true, type: String},
        to: {required: true, type: String},
        variant: {default: 'white', type: String}
    })
    const text = computed(() => `text-${props.variant}`)
</script>

<template>
    <li>
        <AppRouterLink :class="text" :disabled="disabled" :to="to" class="dropdown-item">
            <Fa :brands="brands" :icon="icon"/>
            <slot/>
        </AppRouterLink>
    </li>
</template>
