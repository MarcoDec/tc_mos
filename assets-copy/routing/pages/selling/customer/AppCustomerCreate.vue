<script lang="ts" setup>
    import type {Actions, Getters} from '../../../../store/countries'
    import type {
        Actions as ActionsIncoterms,
        Getters as GettersIncoterms
    } from '../../../../store/incoterms'
    import type {FormField, FormValue} from '../../../../types/bootstrap-5'
    import {computed, defineEmits, onMounted, reactive} from '@vue/runtime-core'
    import {
        useNamespacedActions,
        useNamespacedGetters
    } from 'vuex-composition-helpers'

    const emit = defineEmits<(e: 'update:modelValue', value: FormValue) => void>()

    const value = reactive({pays: 'fr'})

    const fetchCountry = useNamespacedActions<Actions>('countries', [
        'fetchCountry'
    ]).fetchCountry
    const fetchIncoterms = useNamespacedActions<ActionsIncoterms>('incoterms', [
        'fetchIncoterms'
    ]).fetchIncoterms

    const options = useNamespacedGetters<Getters>('countries', ['options']).options
    const optionsIncoterms = useNamespacedGetters<GettersIncoterms>('incoterms', [
        'options'
    ]).options

    const fields = computed<FormField[]>(() => [
        {
            children: [
                {label: 'Nom', name: 'nom '},
                {label: 'Adresse', name: 'adresse'},
                {label: 'complément d\'adresse', name: 'adresse2'},
                {label: 'Code postal ', name: 'code'},
                {
                    label: 'Pays ',
                    name: 'pays ',
                    options: options.value,
                    type: 'select'
                },
                {label: 'Téléphone', name: 'tel', type: 'phone'},
                {label: 'Email', name: 'email'},
                {
                    label: 'incoterms',
                    name: 'incoterms',
                    options: optionsIncoterms.value,
                    type: 'select'
                },
                {label: 'TVA', name: 'tva'},
                {label: 'Quality', name: 'qte'},
                {label: 'copperIndex', name: 'copperIndex'},
                {label: 'copperType', name: 'copperType'}
            ],
            label: 'Créer un nouveau client',
            mode: 'fieldset',
            name: 'client'
        },
        {
            children: [{label: 'Client', name: 'client'}],

            label: 'Ajouter une nouvelle relation client',
            mode: 'fieldset',
            name: 'client'
        }
    ])

    function input(e: Readonly<Event>): void {
        emit('update:modelValue', (e.target as HTMLInputElement).value)
        value.pays = (e.target as HTMLInputElement).value
    }
    onMounted(async () => {
        await fetchCountry()
        await fetchIncoterms()
    })
</script>

<template>
    <AppForm
        id="login"
        v-model="value"
        :fields="fields"
        country-field="pays"
        @input="input"/>
</template>

<style>
.cardOrderSupplier {
  border: 6px solid #1d583d;
}
.overflow-auto {
  overflow: initial !important;
}
</style>
