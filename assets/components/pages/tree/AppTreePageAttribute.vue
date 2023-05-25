<script setup>
    import AppTreeAttributes from '../../tree/attributes/AppTreeAttributes.vue'
    import {onUnmounted} from 'vue'
    import useAttributes from '../../../stores/attribute/attributes'

    defineProps({fields: {required: true, type: Array}, label: {required: true, type: String}})

    const attributes = useAttributes()
    await attributes.fetchOne()
    console.log('aaaaa', attributes)
    onUnmounted(() => {
        attributes.dispose()
    })
</script>

<template>
    <AppTreePage :fields="fields" :label="label">
        <template #default="{tree}">
            <AppTreeAttributes v-if="tree.hasSelected" :attributes="attributes" :tree="tree"/>
        </template>
    </AppTreePage>
</template>
