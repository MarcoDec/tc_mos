<script setup>
    import {CollectionRepository} from '../../../store/modules'
    import {computed} from 'vue'
    import {useRepo} from '../../../composition'

    const props = defineProps({
        coll: {required: true, type: Object},
        field: {required: true, type: Object},
        form: {required: true, type: String}
    })
    const modelValue = computed(() => props.coll.search[props.field.name] ?? null)
    const repo = useRepo(CollectionRepository)

    function input(value) {
        repo.input(props.coll.id, props.field.name, value)
    }
</script>

<template>
    <td>
        <AppInputGuesser
            v-if="field.filter"
            :field="field"
            :form="form"
            :model-value="modelValue"
            @update:model-value="input"/>
    </td>
</template>
