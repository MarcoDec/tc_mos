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
        console.log(props.fields.fields)
        props.fields.fields.forEach(f => {
            if (f.type === 'date') {
                localData.value[f.name] = localData.value[f.name].toISOString().slice(0, 10)
            }
            if (f.type === 'select') {
                if (/\/api\/\w+\/\d+/.test(localData.value[f.name])) {
                    //On ne fait rien
                } else {
                    const theField = f.optionsList.find(o => o.text === localData.value[f.name])
                    //console.log(f.name, theField['@id'], localData.value[f.name])
                    localData.value[f.name] = theField['@id']
                }
            }
        })
        props.store.createBody = localData.value
        console.log(props.fields, localData.value)
        try {
            props.store.create().then(() => {
                props.machine.send('success')
            })
        } catch (violations) {
            props.machine.send('fail', {violations})
        }
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
