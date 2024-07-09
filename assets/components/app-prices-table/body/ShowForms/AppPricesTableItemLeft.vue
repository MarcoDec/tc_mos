<script setup>
    import {defineProps, ref} from 'vue'
    import AppPricesTableUpdateItem from '../UpdateForms/AppPricesTableUpdateItem.vue'
    import AppMainItemShow from './MainItemForms/AppMainItemShow.vue'

    const props = defineProps({
        item: {required: true, type: Object},
        mainFields: {required: true, type: Array},
        form: {required: true, type: String},
        index: {required: true, type: Number},
        rights: {
            required: true,
            type: Object,
            default: () => ({
                update: false,
                delete: false,
                add: false
            })
        }
    })
    const emit = defineEmits(['deleted', 'update', 'annuleUpdate', 'updateItems'])
    // console.log('props.item', props.item)
    const updated = ref(false)

    function update() {
        updated.value = true
    }

    function annuleUpdated() {
        emit('annuleUpdate')
        updated.value = false
    }

    function deleted(item) {
        // console.log('deleted', item)
        emit('deleted', item)
    }

    async function updateItems(item) {
        // console.log('item', item)
        emit('updateItems', item)
        emit('annuleUpdate')
    }
</script>

<template>
    <AppMainItemShow
        v-if="updated === false"
        :index="index"
        :form="`main_${props.item.id}`"
        :main-fields="mainFields"
        :item="item"
        :rights="rights"
        @deleted="deleted"
        @update="update"/>
    <AppPricesTableUpdateItem
        v-else
        :fields="mainFields"
        :form="`main_update_${props.item.id}`"
        :item="item"
        :index="index"
        @annule-update="annuleUpdated"
        @update-items="updateItems"/>
</template>
