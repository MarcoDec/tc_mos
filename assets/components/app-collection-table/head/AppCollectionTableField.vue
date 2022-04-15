<script setup>
    import {computed} from 'vue'

    const emit = defineEmits(['click'])
    const props = defineProps({
        asc: {required: true, type: Boolean},
        field: {required: true, type: Object},
        sort: {required: true, type: String}
    })
    const down = computed(() => ({'text-secondary': props.field.name !== props.sort || props.asc}))
    const up = computed(() => ({'text-secondary': props.field.name !== props.sort || !props.asc}))
    const order = computed(() => (props.asc ? 'ascending' : 'descending'))
    const ariaSort = computed(() => (props.sort ? order.value : 'none'))

    function click() {
        emit('click', props.field)
    }
</script>

<template>
    <th :aria-sort="ariaSort" @click="click">
        <span class="d-flex justify-content-between">
            <span>{{ field.label }}</span>
            <span v-if="field.sort" class="d-flex flex-column">
                <Fa :class="down" icon="caret-up"/>
                <Fa :class="up" icon="caret-down"/>
            </span>
        </span>
    </th>
</template>
