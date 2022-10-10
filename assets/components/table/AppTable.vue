<script setup>
    import AppTableHeaders from './head/AppTableHeaders.vue'
    import AppTableItems from './body/AppTableItems.vue'

    defineProps({fields: {required: true, type: Array}, store: {required: true, type: Object}})
</script>

<template>
    <div class="row">
        <div class="col">
            <table class="table table-bordered table-hover table-responsive table-sm table-striped">
                <AppTableHeaders :fields="fields" :store="store"/>
                <AppTableItems :fields="fields" :store="store"/>
            </table>
        </div>
    </div>
</template>
