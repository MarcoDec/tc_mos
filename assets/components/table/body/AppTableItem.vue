<script setup>
    import AppTableItemRead from './read/AppTableItemRead.vue'
    import AppTableItemUpdate from './update/AppTableItemUpdate.vue'
    import {computed} from 'vue'

    const props = defineProps({
        action: {type: Boolean},
        body: {required: true, type: String},
        disableRemove: {type: Boolean},
        enableShow: {type: Boolean},
        fields: {required: true, type: Object},
        index: {required: true, type: Number},
        item: {required: true, type: Object},
        machine: {required: true, type: Object}
    })
    const id = computed(() => `${props.body}-${props.item.id}`)
    const update = computed(() => props.machine.state.value.matches('update')
        && props.machine.state.value.context.updated === props.item['@id'])
    const emits = defineEmits(['show'])
    function show(data) {
        emits('show', data)
    }
</script>

<template>
    <AppTableItemUpdate v-if="update" :id="id" :fields="fields" :index="index" :item="item" :machine="machine"/>
    <AppTableItemRead
        v-else
        :id="id"
        :action="action"
        :disable-remove="disableRemove"
        :enable-show="enableShow"
        :fields="fields"
        :index="index"
        :item="item"
        :send="machine.send"
        @show="show"/>
</template>
