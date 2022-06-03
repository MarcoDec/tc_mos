<script setup>
    import {computed, onMounted, ref, watch} from 'vue'
    import {generateFields} from '../props'

    const props = defineProps({
        families: {required: true, type: Object},
        fields: generateFields(),
        id: {required: true, type: String},
        machine: {required: true, type: Object}
    })
    const fields = computed(() => [
        {label: 'Parent', name: 'parent', options: props.families, type: 'select'},
        ...props.fields
    ])
    const selected = computed(() => props.families.selected)
    const selectedForm = computed(() => selected.value?.form(fields.value) ?? {file: '/img/no-image.png'})
    const title = computed(() => selected.value?.fullName ?? 'Ajouter une famille')
    const value = ref(null)
    const formId = computed(() => `${props.id}-create`)

    function blur() {
        props.machine.send('submit')
        props.families.blur()
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
                await props.families.create(data)
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
                label-cols="col-xl-3 col-lg-12"
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
