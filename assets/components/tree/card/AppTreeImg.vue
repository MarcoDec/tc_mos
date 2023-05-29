<script setup>
    import {computed} from 'vue'

    const props = defineProps({tree: {required: true, type: Object}})
    const selectedKey = computed(() => props.tree.selectedKey)
    const hasImage = computed(() => {
        if (typeof selectedKey.value === 'undefined' || isNaN(selectedKey.value)) return false
        const nodeKey = selectedKey.value - 1
        return props.tree.nodes[nodeKey].filePath
    })
    const imageUrl = computed(() => {
        const nodeKey = selectedKey.value - 1
        if (hasImage.value) return props.tree.nodes[nodeKey].filePath
        return null
    })
</script>

<template>
    <img v-if="hasImage" :src="imageUrl"/>
    <div v-else class="alert alert-primary">
        Aucune Image
    </div>
</template>
