<script setup>
    import {computed, onMounted, ref, watch} from 'vue'
    import useFamiliesStore from '../../stores/purchase/component/family/families'

    const families = useFamiliesStore()
    const fields = computed(() => [
        {label: 'Parent', name: 'parent', options: families, type: 'select'},
        {label: 'Code', name: 'code'},
        {label: 'Nom', name: 'name'},
        {label: 'Cuivre', name: 'copperable', type: 'boolean'},
        {label: 'Code douanier', name: 'customsCode'},
        {label: 'Icône', name: 'file', type: 'file'}
    ])
    const props = defineProps({id: {required: true, type: String}, machine: {required: true, type: Object}})
    const selected = computed(() => families.selected)
    const selectedForm = computed(() => selected.value?.form(fields.value) ?? {file: '/img/no-image.png'})
    const title = computed(() => selected.value?.fullName ?? 'Ajouter une famille')
    const value = ref(null)
    const formId = computed(() => `${props.id}-create`)

    function blur() {
        props.machine.send('submit')
        families.blur()
        props.machine.send('success')
    }

    async function remove() {
        if (selected.value)
            await selected.value.remove()
    }

    async function submit(data) {
        props.machine.send('submit')
        try {
            if (selected.value)
                await selected.value.update(data)
            else
                await families.create(data)
            props.machine.send('success')
        } catch (violations) {
            props.machine.send('fail', {violations})
        }
    }

    function updateValue() {
        value.value = {...selectedForm.value}
    }

    updateValue()

    onMounted(updateValue)
    watch(selectedForm, updateValue)
</script>

<template>
    <AppCard :id="id" :title="title">
        <div class="row">
            <AppForm
                :id="formId"
                v-model="value"
                :disabled="machine.state.value.matches('loading')"
                :fields="fields"
                :no-ignore-null="families.hasSelected"
                :violations="machine.state.value.context.violations"
                class="col"
                submit-label="Créer"
                @submit="submit">
                <template #default="{disabled, form, submitLabel, type}">
                    <template v-if="families.hasSelected">
                        <AppBtn :disabled="disabled" :form="form" :type="type" class="me-2">
                            Modifier
                        </AppBtn>
                        <AppBtn :disabled="disabled" class="me-2" variant="warning" @click="blur">
                            Annuler
                        </AppBtn>
                        <AppBtn :disabled="disabled" variant="danger" @click="remove">
                            Supprimer
                        </AppBtn>
                    </template>
                    <AppBtn v-else :disabled="disabled" :form="form" :type="type">
                        {{ submitLabel }}
                    </AppBtn>
                </template>
            </AppForm>
            <div class="col-4 position-relative">
                <img :src="value.file" class="img-thumbnail position-absolute start-50 top-50 translate-middle"/>
            </div>
        </div>
    </AppCard>
</template>
