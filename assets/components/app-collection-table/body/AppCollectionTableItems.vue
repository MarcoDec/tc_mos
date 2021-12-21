<script lang="ts" setup>
    import type {FormValues} from '../../../types/bootstrap-5'
    import {defineProps} from 'vue'

    defineProps<{items: FormValues[]}>()
</script>

<template>
    <tbody>
        <AppCollectionTableItem v-for="(item, index) in items" :key="item.id" :index="index" :item="item"/>
    </tbody>
</template>
