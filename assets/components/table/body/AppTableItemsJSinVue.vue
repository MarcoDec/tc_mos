<script setup>
    import AppTableItemJSinVue from './AppTableItemJSinVue.vue'
    import AppTableItemUpdateJS from './update/AppTableItemUpdateJS'
    import {generateTableFields} from '../../props'

    defineProps({
        fields: generateTableFields(),
        id: {required: true, type: String},
        items: {required: true, type: Object},
        machine: {required: true, type: Object},
        options: {default: () => ({delete: true, modify: true, show: false}), required: false, type: Object}
    })
    // const children = {}
    // function generateSlot(field) {
    //     const slotName = `cell(${field.name})`
    //     const slot = context.slots[slotName]
    //     if (typeof slot === 'function')
    //         children[slotName] = args => slot(args)
    // }
</script>

<template>
    <tbody :id="id">
        <template v-for="(item, index) in items">
            <AppTableItemUpdateJS
                v-if="machine.state.value.matches('update') && machine.state.value.context.updated === item['@id']"
                :id="`${id}-${item.id}`"
                :key="item['@id']"
                :fields="fields"
                :index="index"
                :item="item"
                :machine="machine"
                :options="options">
                <slot v-for="field in fields" :name="`cell(${field.name})`"/>
            </AppTableItemUpdateJS>
            <AppTableItemJSinVue
                v-else
                :id="`${id}-${item.id}`"
                :key="item['@id']"
                :fields="fields"
                :index="index"
                :item="item"
                :machine="machine"
                :options="options">
                <slot v-for="field in fields" :name="`cell(${field.name})`"/>
            </AppTableItemJSinVue>
        </template>
    </tbody>
</template>
