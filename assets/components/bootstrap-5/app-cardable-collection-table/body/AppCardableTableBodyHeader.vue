<script setup>
    import {computed, ref} from 'vue'
    import clone from 'clone'

    const emit = defineEmits(['cancelSearch', 'search', 'update:model-value'])
    const props = defineProps({
        fields: {required: true, type: Array},
        form: {required: true, type: String},
        // eslint-disable-next-line vue/no-unused-properties
        modelValue: {default: null, type: [Array, Boolean, Number, String, Object]}
    })
    //console.log('props.fields', props.fields)
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
    function onUpdateModelValue(event, fieldName) {
        emit('update:model-value', {field: fieldName, event})
    }
</script>

<template>
    <tr class="header">
        <td class="px100">
            <button class="btngris" @click="search">
                <Fa icon="search"/>
            </button>
            <button class="btntimes" @click="cancelSearch">
                <Fa icon="times"/>
            </button>
        </td>

        <td v-for="field in tabFields" :key="field.name" :style="{width: field.width ? `${field.width}px` : null}">
            <template v-if="field.filter !== false">
                <AppInputGuesser v-if="!field.searchDisabled" :id="field.name" v-model="inputValues[field.name]" :form="form" :field="field" @update:model-value="onUpdateModelValue($event, field.name)" @keyup.enter="search"/>
            </template>
            <template v-else>
                {{ inputValues[field.name] }}
            </template>
        </td>
    </tr>
</template>

<style scoped>
    .px50{
        width: 50px;
    }
    .px100{
        width: 100px;
    }
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
