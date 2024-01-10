<script setup>
    import {computed, onUnmounted} from 'vue'
    import useFields from '../../../stores/field/fields'

    const props = defineProps({attributes: {required: true, type: Object}, tree: {required: true, type: Object}})
    const fields = useFields('attributes', props.attributes.fields)
    const form = computed(() => `attributes-${props.tree.selected.id}`)
    const modelValue = computed(() => props.attributes.modelValue(props.tree.selected))

    async function submit(data) {
        const attributes = []
        for (const [attribute, checked] of Object.entries(data))
            if (checked)
                attributes.push(attribute)
        props.attributes.update(await props.tree.selected.updateAttributes({attributes}))
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
