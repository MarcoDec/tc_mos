<script setup>
    import {computed, defineEmits, defineProps} from 'vue'

    import clone from 'clone'

    const props = defineProps({
        create: {type: Boolean},
        fields: {required: true, type: Array},
        form: {required: true, type: String},
        user: {required: true, type: String}
    })
    const tabFields = computed(() => props.fields.map(element => {
        const cloned = clone(element)

        if (cloned.type === 'boolean'){
            cloned.type = 'grpbutton'
        }
        return cloned
    }))
    const emit = defineEmits(['open'])

    function ajout(){
        emit('open')
    }
</script>

<template>
    <tr class="header">
        <th scope="row" class="">
            <Fa icon="filter"/>
        </th>
        <td>
            <button v-if="create && user !== 'reader'" class="btngris" @click="ajout">
                <Fa icon="plus-circle"/>
            </button>
            <button class="btngris">
                <Fa icon="search"/>
            </button>
            <button class="btntimes">
                <Fa icon="times"/>
            </button>
        </td>

        <td v-for="field in tabFields" :key="field.name">
            <AppInputGuesser :id="field.name" :form="form" :field="field"/>
        </td>
    </tr>
</template>

<style scoped>
.header{
    background-color: #c5c5c5 ;
}
.btngris{
    width: 24px;
    height: 24px;
    margin-left: 2px;
    margin-bottom: 4px;
    color: #fff;
    background-color: #6c757d;
    border-color: #6c757d;
    padding-left: 2px;
    padding-bottom: 24px;
}
.btntimes{
    width: 24px;
    height: 24px;
    margin-left: 2px;
    margin-bottom: 4px;
    color: #fff;
    background-color: #dc3545;
    border-color: #dc3545;
    padding-left: 4px;
    padding-bottom: 24px;
}
</style>
