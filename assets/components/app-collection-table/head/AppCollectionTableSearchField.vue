<script setup>
    import {CollectionRepository} from '../../../store/modules'
    import {computed} from 'vue'
    import {useRepo} from '../../../composition'

    const props = defineProps({
        coll: {required: true, type: Object},
        field: {required: true, type: Object},
        form: {required: true, type: String}
    })
    const inputId = computed(() => `${props.form}-${props.field.name}`)
    const modelValue = computed(() => props.coll.search[props.field.name] ?? null)
    const searchField = computed(() => (props.field.type === 'boolean'
        ? {...props.field, type: 'search-boolean'}
        : props.field
    ))
    const repo = useRepo(CollectionRepository)

    function input(value) {
        repo.input(props.coll.id, props.field.name, value)
    }
</script>

<template>
    <td>
        <AppInputGuesser
            v-if="field.filter"
            :id="inputId"
            :field="searchField"
            :form="form"
            :model-value="modelValue"
            @update:model-value="input"/>
    </td>
</template>
