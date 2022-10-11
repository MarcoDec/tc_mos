<script setup>
    import AppTableItemRead from './AppTableItemRead.vue'
    import AppTableItemUpdate from './AppTableItemUpdate.vue'
    import {computed} from 'vue'

    const props = defineProps({
        body: {required: true, type: String},
        fields: {required: true, type: Array},
        index: {required: true, type: Number},
        item: {required: true, type: Object},
        machine: {required: true, type: Object}
    })
    const id = computed(() => `${props.body}-${props.item.id}`)
    const update = computed(() => props.machine.state.value.matches('update')
        && props.machine.state.value.context.updated === props.item['@id'])
</script>

<template>
    <AppTableItemUpdate v-if="update" :id="id" :fields="fields" :index="index" :item="item" :machine="machine"/>
    <AppTableItemRead v-else :id="id" :fields="fields" :index="index" :item="item" :send="machine.send"/>
</template>
