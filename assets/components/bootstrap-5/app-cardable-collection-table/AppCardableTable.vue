<script setup>
    import {computed, defineEmits, defineProps, ref} from 'vue'
    import AppCardableTableBodyHeader from './body/AppCardableTableBodyHeader.vue'
    import AppCardableTableBodyItem from './body/AppCardableTableBodyItem.vue'
    import AppCardableTableHeader from './head/AppCardableTableHeader.vue'

    const props = defineProps({
        create: {type: Boolean},
        fields: {required: true, type: Array},
        form: {required: true, type: String},
        items: {required: true, type: Array},
        min: {type: Boolean},
        pag: {type: Boolean},
        user: {required: true, type: String}
    })
    const displayedFileds = computed(() => (props.min ? props.fields.filter(({min}) => min) : props.fields))
    const emit = defineEmits(['deleted', 'update'])
    function update(item){
        emit('update', item)
    }
    function deleted(id){
        emit('deleted', id)
    }

</script>

<template>
    <table class="table table-bordered table-hover table-striped">
        <AppCardableTableHeader :fields="displayedFileds"/>
        <tbody>
            <AppCardableTableBodyHeader :form="form" :create="create" :fields="displayedFileds" :user="user"/>
            <tr class="bg-dark">
                <td colspan="10"/>
            </tr>
            <AppCardableTableBodyItem :items="items" :fields="displayedFileds" @update="update" @deleted="deleted"/>
        </tbody>
    </table>
    <nav v-if="pag" aria-label="Page navigation example">
        <ul class="pagination">
            <li class="page-item">
                <a class="page-link" href="#">Début</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#">Préc.</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#">1</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#">2</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#">3</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#">Suiv.</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#">Fin</a>
            </li>
        </ul>
    </nav>
</template>

<style scoped>
.pagination{
    float: right;
}
</style>
