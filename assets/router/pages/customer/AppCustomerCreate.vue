<script setup>
    import {computed, onMounted, provide, ref} from 'vue-demi'
    import useCountries from '../../../stores/countries/countries'

    const emit = defineEmits(['update:modelValue'])
    const pays = ref('fr')
    const val = computed(() => pays.value)
    const countries = useCountries()
    provide('country', val)
    onMounted(async () => {
        await countries.fetch()
    })
    const fields = computed(() => [
        {
            children: [
                {label: 'Nom', name: 'nom '},
                {label: 'Adresse', name: 'adresse'},
                {label: 'complément d\'adresse', name: 'adresse2'},
                {label: 'Code postal ', name: 'code'},
                {
                    label: 'Pays ',
                    name: 'pays ',
                    options: countries,
                    type: 'select'
                },
                {label: 'Téléphone', name: 'tel', type: 'phone'},
                {label: 'Email', name: 'email'},
                {
                    label: 'incoterms',
                    name: 'incoterms'
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

    function input(e) {
        emit('update:modelValue', e.target.value)
        pays.value = e.target.value
    }
</script>

<template>
    <AppForm id="customer" :fields="fields" country-field="pays" @input="input"/>
</template>

<style>
.cardOrderSupplier {
  border: 6px solid #1d583d;
}
</style>
