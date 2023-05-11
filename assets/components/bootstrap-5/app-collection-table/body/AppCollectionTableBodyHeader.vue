<script setup>
    import {computed, defineProps} from 'vue'
    import clone from 'clone'

    const emit = defineEmits(['cancelSearch', 'search', 'open'])
    const props = defineProps({
        fields: {required: true, type: Array},
        form: {required: true, type: String},
        modelValue: {default: null, type: [Array, Boolean, Number, String, Object]}
    })
    const inputValues = {}
    const tabFields = computed(() =>
        props.fields.map(element => {
            const cloned = clone(element)

            if (cloned.type === 'boolean') {
                cloned.type = 'grpbutton'
            }
            return cloned
        }))
    function search() {
        emit('search', inputValues)
    }
    // async function cancelSearch() {
    //     inputValues = {}
    //     emit('cancelSearch', inputValues)
    // }

    function ajout() {
        emit('open')
    }
</script>

<template>
    <tr class="header">
        <th scope="row" class="">
            <Fa icon="search"/>
        </th>
        <td>
            <AppBtn icon="plus" label="Ajouter" @click="ajout"/>
            <AppBtn icon="search" label="Modifier" @click="search"/>
        </td>

        <td v-for="field in tabFields" :key="field.name">
            <AppInputGuesser
                :id="field.name"
                v-model="inputValues[field.name]"
                :form="form"
                :field="field"
                :update:model-value="modelValue"/>
        </td>
    </tr>
</template>

<style scoped>
.header {
  background-color: #c5c5c5;
}
.btngris {
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
.btntimes {
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
