<script setup>
    import AppTableHeadersJS from './head/AppTableHeadersJS'
    import AppTableItemsJSinVue from './body/AppTableItemsJSinVue.vue'
    import {generateTableFields} from '../props'

    defineProps({
        fields: generateTableFields(),
        id: {required: true, type: String},
        items: {default: 'items', type: String},
        machine: {required: true, type: Object},
        options: {default: () => ({delete: true, modify: false, show: true}), required: false, type: Object},
        store: {required: true, type: Object}
    })
</script>

<template>
    <div :id="id">
        <div class="row">
            <table class="col table table-bordered table-hover table-responsive table-sm table-striped">
                <AppTableHeadersJS
                    :id="`${id}-headers`"
                    :fields="fields"
                    :machine="machine"
                    :store="store">
                    <slot v-for="field in fields" :name="`search(${field.name})`"/>
                </AppTableHeadersJS>
                <AppTableItemsJSinVue
                    :id="`${id}-items`"
                    :fields="fields"
                    :items="store[items]"
                    :machine="machine"
                    :options="options">
                    <slot v-for="field in fields" :name="`cell(${field.name})`"/>
                </AppTableItemsJSinVue>
            </table>
        </div>
    </div>
</template>
