<script setup>
    import {computed, onUnmounted, toRefs} from 'vue'
    import useFields from '../../../stores/field/fields'

    const {attributes, tree} = toRefs(defineProps({
        attributes: {required: true, type: Object},
        tree: {required: true, type: Object}
    }))
    const fields = useFields('attributes', attributes.value.fields)
    const form = computed(() => `attributes-${tree.value.selected.id}`)
    const modelValue = computed(() => attributes.value.modelValue(tree.value.selected))

    async function submit(data) {
        const localAttributes = []
        for (const [attribute, checked] of Object.entries(data))
            if (checked)
                localAttributes.push(attribute)
        attributes.value.update(await tree.value.selected.updateAttributes({localAttributes}))
    }

    onUnmounted(() => {
        fields.dispose()
    })
</script>

<template>
    <AppCard title="Attributs">
        <AppFormGenerator :id="form" :fields="fields" :model-value="modelValue" @submit="submit"/>
    </AppCard>
</template>
