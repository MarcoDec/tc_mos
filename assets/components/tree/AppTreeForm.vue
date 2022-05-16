<script setup>
    import {computed, ref} from 'vue'
    import useFamiliesStore from '../../stores/purchase/component/family/families'

    const families = useFamiliesStore()
    const fields = computed(() => [
        {label: 'Parent', name: 'parent', options: families.options, type: 'select'},
        {label: 'Code', name: 'code'},
        {label: 'Nom', name: 'name'},
        {label: 'Cuivre', name: 'copperable'},
        {label: 'Code douanier', name: 'customsCode'},
        {label: 'Icône', name: 'file', type: 'file'}
    ])
    const props = defineProps({id: {required: true, type: String}, machine: {required: true, type: Object}})
    const value = ref({file: '/img/no-image.png'})
    const form = computed(() => `${props.id}-create`)

    async function submit(data) {
        props.machine.send('submit')
        try {
            await families.create(data)
            props.machine.send('success')
        } catch (violations) {
            props.machine.send('fail', {violations})
        }
    }
</script>

<template>
    <AppCard :id="id" title="Ajouter une famille">
        <div class="row">
            <AppForm
                :id="form"
                v-model="value"
                :disabled="machine.state.value.matches('loading')"
                :fields="fields"
                :violations="machine.state.value.context.violations"
                class="col"
                submit-label="Créer"
                @submit="submit"/>
            <div class="col-4 position-relative">
                <img :src="value.file" class="img-thumbnail position-absolute start-50 top-50 translate-middle"/>
            </div>
        </div>
    </AppCard>
</template>
