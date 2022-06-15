<script setup>
    import {CollectionRepository} from '../../../store/modules'
    import {computed} from 'vue'
    import {useRepo} from '../../../composition'

    const emit = defineEmits(['click'])
    const props = defineProps({coll: {required: true, type: Object}, field: {required: true, type: Object}})
    const sorted = computed(() => props.field.name === props.coll.sort)
    const down = computed(() => ({'text-secondary': !sorted.value || props.coll.asc}))
    const up = computed(() => ({'text-secondary': !sorted.value || !props.coll.asc}))
    const order = computed(() => (props.coll.asc ? 'ascending' : 'descending'))
    const ariaSort = computed(() => (sorted.value ? order.value : 'none'))
    const repo = useRepo(CollectionRepository)

    function click() {
        repo.sort(props.coll, props.field)
        emit('click')
    }
</script>

<template>
    <th :aria-sort="ariaSort" @click="click">
        <span class="d-flex justify-content-between">
            <span>{{ field.label }}</span>
            <span class="d-flex flex-column">
                <Fa :class="down" icon="caret-up"/>
                <Fa :class="up" icon="caret-down"/>
            </span>
        </span>
    </th>
</template>
