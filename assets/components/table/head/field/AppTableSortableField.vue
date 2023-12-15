<script setup>
    import {computed} from 'vue'

    const props = defineProps({
        field: {required: true, type: Object},
        send: {required: true, type: Function},
        store: {required: true, type: Object}
    })
    const ariaSort = computed(() => props.store.ariaSort(props.field))
    const sorted = computed(() => props.store.isSorter(props.field))
    const down = computed(() => ({'text-secondary': !sorted.value || props.store.asc}))
    const up = computed(() => ({'text-secondary': !sorted.value || !props.store.asc}))

    async function sort() {
        props.send('search')
        props.send('submit')
        await props.store.sort(props.field)
        props.send('success')
    }
</script>

<template>
    <th :aria-sort="ariaSort" @click="sort">
        <span class="display-flex justify-content-between">
            <span>{{ field.label }}</span>
            <span class="d-flex flex-column">
                <Fa :class="up" icon="caret-up"/>
                <Fa :class="down" icon="caret-down"/>
            </span>
        </span>
    </th>
</template>
