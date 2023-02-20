<script setup>
    defineProps({
        fields: {required: true, type: Array},
        label: {required: true, type: String},
        tag: {default: 'AppTreePage', type: [String, Object]}
    })
</script>

<template>
    <AppSuspense>
        <component :is="tag" :fields="fields" :label="label"/>
    </AppSuspense>
</template>
