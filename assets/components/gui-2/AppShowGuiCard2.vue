<script lang="ts" setup>
    import type {BootstrapVariant} from '../../types/bootstrap-5'
    import {defineProps} from 'vue'

    defineProps<{bgVariant: BootstrapVariant, cardClass?: string}>()
</script>

<template>
    <AppCol>
        <AppCard :bg-variant="bgVariant" :class="cardClass" class="overflow-hidden">
            <slot/>
        </AppCard>
    </AppCol>
</template>
