<script setup>
    import {computed} from 'vue'

    const props = defineProps({field: {required: true, type: Object}, store: {required: true, type: Object}})
    const ariaSort = computed(() => props.store.ariaSort(props.field))
    const sorted = computed(() => props.store.isSorter(props.field))
    const down = computed(() => ({'text-secondary': !sorted.value || props.store.asc}))
    const up = computed(() => ({'text-secondary': !sorted.value || !props.store.asc}))
</script>

<template>
    <th :aria-sort="ariaSort">
        <span class="d-flex justify-content-between">
            <span>{{ field.label }}</span>
            <span class="d-flex flex-column">
                <Fa :class="down" icon="caret-up"/>
                <Fa :class="up" icon="caret-down"/>
            </span>
        </span>
    </th>
</template>
