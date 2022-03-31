<script setup>
    import {computed, inject, ref} from 'vue'

    const props = defineProps({
        index: {required: true, type: Number},
        item: {required: true, type: Object}
    })
    const fields = inject('fields', [])
    const formattedIndex = computed(() => props.index + 1)
    const show = ref(true)
    const td = computed(() => (show.value ? 'AppCollectionTableItemField' : 'AppCollectionTableItemInput'))
    const emit = defineEmits(['update'])

    function toggle() {
        show.value = !show.value
    }

    function update() {
        emit('update', props.item)
    }
</script>

<template>
    <tr>
        <td class="text-center">
            {{ formattedIndex }}
        </td>
        <td v-if="show" class="text-center">
            <AppBtn v-if="item.update" icon="pencil-alt" variant="primary" @click="toggle"/>
            <AppBtn v-if="item.update2" icon="eye" variant="secondary" @click="update"/>
            <AppBtn v-if="item['delete']" icon="trash" variant="danger"/>
        </td>
        <td v-else class="text-center">
            <AppBtn icon="check" variant="success"/>
            <AppBtn icon="times" variant="danger" @click="toggle"/>
        </td>
        <component :is="td" v-for="field in fields" :key="field.name" :field="field" :item="item"/>
    </tr>
</template>
