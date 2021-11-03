<script lang="ts" setup>
    import {reactive, ref} from 'vue'
    const json = {'CAB-Cable': {'prop1': 'CAB-Fil', 'prop2': 'CAB-Cordons', 'prop3': 'CAB-Divers'}, 'array': ['A', 'B', 'C'], 'number': 123, 'object': {'nestedObject': {'prop1': 'value2'}, 'prop2': 'value1'}}
    const data = JSON.stringify(json)
    const selected = ref(false)
    const state = reactive({
        data
    })

    function select(): boolean {
        selected.value = true
        console.log('selected', selected.value)
        return selected.value
    }
</script>

<template>
    <h1>
        <Fa icon="layer-group"/>
        <span id="nameInterface"> Famille</span>
    </h1>
    <div id="row1" class="row">
        <div v-if="!selected" class="col-6">
            <JsonTreeView :data="state.data" :max-depth="3" @selected="select"/>
        </div>
        <div v-else class="col-4">
            <JsonTreeView :data="state.data" :max-depth="3" @selected="select"/>
        </div>
        <div v-if="!selected" class="col-6">
            <AppComponentFamiliesAddCard/>
        </div>
        <div v-else class="col-8">
            <AppComponentFamiliesCard/>
        </div>
    </div>
</template>
