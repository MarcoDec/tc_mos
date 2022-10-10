<script setup>
    import AppTableHeaders from './head/AppTableHeaders.vue'
    import AppTableItems from './body/AppTableItems.vue'
    import {computed} from 'vue'

    const props = defineProps({
        fields: {required: true, type: Array},
        id: {required: true, type: String},
        machine: {required: true, type: Object},
        store: {required: true, type: Object}
    })
    const headers = computed(() => `${props.id}-headers`)
</script>

<template>
    <div :id="id" class="row">
        <div class="col">
            <table class="table table-bordered table-hover table-responsive table-sm table-striped">
                <AppTableHeaders :id="headers" :fields="fields" :machine="machine" :store="store"/>
                <AppTableItems :fields="fields" :send="machine.send" :store="store"/>
            </table>
        </div>
    </div>
</template>
