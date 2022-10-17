<script setup>
    import AppTableHeaders from './head/AppTableHeaders.vue'
    import AppTableItems from './body/AppTableItems.vue'
    import {computed} from 'vue'
    import {useSlots} from '../../composable/table'

    const props = defineProps({
        disableRemove: {type: Boolean},
        fields: {required: true, type: Object},
        id: {required: true, type: String},
        machine: {required: true, type: Object},
        store: {required: true, type: Object}
    })
    const action = computed(() => props.fields.action || !props.disableRemove)
    const body = computed(() => `${props.id}-body`)
    const headers = computed(() => `${props.id}-headers`)
    const {slots} = useSlots(props.fields.fields)
</script>

<template>
    <div :id="id" class="row">
        <div class="col">
            <table class="table table-bordered table-hover table-responsive table-sm table-striped">
                <AppTableHeaders :id="headers" :action="action" :fields="fields" :machine="machine" :store="store">
                    <template v-for="s in slots" :key="s.name" #[s.slot]="args">
                        <slot :name="s.slot" v-bind="args"/>
                    </template>
                </AppTableHeaders>
                <AppTableItems
                    :id="body"
                    :action="action"
                    :disable-remove="disableRemove"
                    :fields="fields"
                    :machine="machine"
                    :store="store"/>
            </table>
        </div>
    </div>
</template>
