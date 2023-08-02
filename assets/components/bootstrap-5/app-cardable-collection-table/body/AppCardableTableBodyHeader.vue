<script setup>
    import {computed, defineProps, ref} from 'vue'
    import clone from 'clone'

    const emit = defineEmits(['cancelSearch', 'search'])
    const props = defineProps({
        fields: {required: true, type: Array},
        form: {required: true, type: String}
    })
    const inputValues = ref([])
    const tabFields = computed(() => props.fields.map(element => {
        const cloned = clone(element)
        /*
        if (cloned.type === 'boolean'){
            cloned.type = 'grpbutton'
        }*/
        return cloned
    }))
    function search() {
        emit('search', inputValues.value)
    }
    async function cancelSearch() {
        inputValues.value = []
        emit('cancelSearch', inputValues.value)
    }
</script>

<template>
    <tr class="header">
        <th scope="row" class="">
            <Fa icon="filter"/>
        </th>
        <td>
            <button class="btngris" @click="search">
                <Fa icon="search"/>
            </button>
            <button class="btntimes" @click="cancelSearch">
                <Fa icon="times"/>
            </button>
        </td>

        <td v-for="field in tabFields" :key="field.name">
            <template v-if="field.filter !== false">
                <AppInputGuesser
                    :id="field.name"
                    v-model="inputValues[field.name]"
                    :form="form"
                    :field="field"/>
            </template>
            <template v-else>
                {{ inputValues[field.name] }}
            </template>
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
