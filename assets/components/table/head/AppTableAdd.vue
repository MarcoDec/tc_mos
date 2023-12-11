<script setup>
    import {onMounted, ref} from 'vue'

    const props = defineProps({
        fields: {required: true, type: Object},
        id: {required: true, type: String},
        machine: {required: true, type: Object},
        store: {required: true, type: Object}
    })
    const localData = ref({})

    onMounted(() => {
        localData.value = props.store.createBody
    })

    function create() {
        props.machine.send('submit')
        props.fields.fields.forEach(f => {
            if (f.type === 'date') {
                if (localData.value[f.name].length > 10)
                    localData.value[f.name] = localData.value[f.name].toISOString().slice(0, 10)
            }
            if (f.type === 'select') {
                const newValue = localData.value[f.name]
                if (typeof newValue === 'object') {
                    const theField = f.optionsList.find(o => o.text === newValue)
                    if (typeof theField === 'undefined') localData.value[f.name] = null
                    else localData.value[f.name] = theField['@id']
                }
                else {
                    localData.value[f.name] = newValue
                }
            }
         })
        props.store.createBody = localData.value
        try {
            props.store.create().then(() => {
                props.machine.send('success')
            })
        } catch (violations) {
            props.machine.send('fail', {violations})
        }
    }
    function onInput(event) {
        const newValue = event.target.value
        const fieldName = event.target.name
        props.fields.fields.forEach(f => {
            if (f.name === fieldName) {
                if (f.type === 'select') {
                    if (typeof newValue === 'object') {
                        const theField = f.optionsList.find(o => o.text === newValue)
                        if (typeof theField === 'undefined') localData.value[f.name] = null
                        else localData.value[f.name] = theField['@id']
                    }
                    else {
                        localData.value[f.name] = newValue
                    }
                }
            }
        })
    }
</script>

<template>
    <AppTableHeaderForm
        :id="id"
        v-model="localData"
        :fields="fields"
        :send="machine.send"
        :store="store"
        :submit="create"
        :violations="machine.state.value.context.violations"
        can-reverse
        class="table-success"
        icon="plus"
        label="Ajouter"
        mode="create"
        @input="onInput"
        reverse-icon="search"
        reverse-label="recherche"
        reverse-mode="search"
        variant="success">
        <template #form="args">
            <slot name="btn" v-bind="args"/>
        </template>
        <template v-for="f in fields.fields" :key="f.name" #[f.name]="args">
            <slot :name="f.name" v-bind="args"/>
        </template>
    </AppTableHeaderForm>
</template>
