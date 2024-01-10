<script setup>
    import useOptions from '../../../stores/option/options'
    import AppAttributeCreate from './AppAttributeCreate.vue'
    import AppFormJS from '../../../components/form/AppFormJS.js'
    import AppSuspense from '../../../components/AppSuspense.vue'
    import AppTab from '../../../components/tab/AppTab.vue'
    import AppTabs from '../../../components/tab/AppTabs.vue'
    import {computed} from 'vue-demi'
    import useComponentFamilyStore from '../../../stores/component/componentFamily'
    import useUnitsStore from '../../../stores/unit/units'

    defineProps({
        fieldsAttributs: {required: true, type: Array},
        myBooleanFamily: {required: true, type: Boolean}
    })

    const emit = defineEmits(['update:modelValue', 'dataAttribute'])
    const storeComponentFamilly = useComponentFamilyStore()
    await storeComponentFamilly.getComponentFamily()
    const storeUnits = useUnitsStore()
    await storeUnits.getUnits()

    const familyOptions = useOptions('component-families')
    await familyOptions.fetchOp()
    //console.log(familyOptions.options)
    const listFamilies = storeComponentFamilly.familiesOption
    const listUnits = storeUnits.unitsOption
    const listUnitSelect = storeUnits.unitsSelect
    let changed = 0

    const fields = computed(() => [
        {
            active: true,
            children: [
                {
                    children: [
                        {label: 'Désignation ', name: 'name'},
                        {
                            label: 'Famille',
                            name: 'family',
                            options: {
                                label: value => familyOptions.options.find(option => option['@id'] === value)?.text ?? null,
                                options: familyOptions.options
                            },
                            type: 'select'
                        },
                        {
                            label: 'Unité',
                            name: 'unit',
                            options: {
                                label: value =>
                                    listUnitSelect.find(option => option.type === value)?.text ?? null,
                                options: listUnitSelect
                            },
                            type: 'select'
                        },
                        {
                            label: 'poids (g) ',
                            name: 'weight',
                            options: {
                                label: value =>
                                    listUnits.find(option => option.type === value)?.text ?? null,
                                options: listUnits
                            },
                            type: 'measureSelect'
                        }
                    ],
                    label: 'General',
                    mode: 'fieldset',
                    name: 'General'
                },
                {
                    children: [
                        {label: 'Fabricant ', name: 'manufacturer'},
                        {label: 'Référence du Fabricant', name: 'manufacturerCode'}
                    ],
                    label: 'Fabricant',
                    mode: 'fieldset',
                    name: 'Fabricant'
                }
            ],
            icon: 'puzzle-piece',
            label: 'Composant',
            mode: 'tab',
            name: 'composant'
        },
        // {
        //     children: [
        //         {label: 'Référence Fournisseur', name: 'refFournisseur'},
        //         {label: 'Incoterms   ', name: 'incoterms'},
        //         {label: 'Délai de livraison moyen (jour ouvrés) ', name: 'delai'},
        //         {label: 'Moq ', name: 'moq', type: 'number'},
        //         {label: 'Quantité par conditionnement ', name: 'qte'},
        //         {label: 'Type de conditionnement ', name: 'type'}
        //     ],
        //     icon: 'puzzle-piece',
        //     label: 'Fournisseur',
        //     mode: 'tab',
        //     name: 'Fournisseur'
        // },
        // {
        //     children: [
        //         {
        //             children: [
        //                 {label: 'Désignation ', name: 'designation'},
        //                 {label: 'Famille', name: 'famille'},
        //                 {label: 'Unité', name: 'unite'},
        //                 {label: 'Poids (g) ', name: 'code'}
        //             ],
        //             label: 'Prix',
        //             mode: 'fieldset',
        //             name: 'prix'
        //         }
        //     ],
        //     icon: 'puzzle-piece',
        //     label: 'Prix',
        //     mode: 'tab',
        //     name: 'prix'
        // },
        {
            icon: 'at',
            label: 'Attributs',
            mode: 'tab',
            name: 'attributs'
        }
    ])
    const formInput = {}
    function input(value) {
        const key = Object.keys(value)[0]
        if (Object.prototype.hasOwnProperty.call(formInput, key)) {
            // if (formInput.hasOwnProperty(key)) {
            if (typeof value[key] === 'object') {
                if (typeof value[key].value !== 'undefined') {
                    const inputValue = parseFloat(value[key].value)
                    formInput[key] = {...formInput[key], value: inputValue}
                }
                if (typeof value[key].code !== 'undefined') {
                    const inputCode = value[key].code
                    formInput[key] = {...formInput[key], code: inputCode}
                }
            } else {
                formInput[key] = value[key]
            }
        } else {
            formInput[key] = value[key]
        }
        emit('update:modelValue', formInput)
        changed++
    }
    function inputAttribute(data) {
        emit('dataAttribute', data)
    }
</script>

<template>
    <AppSuspense>
        <div class="gui-card">
            <AppTabs id="gui-form-create" class="display-block-important">
                <AppTab v-for="(field, index) in fields" :active="index===0" :id="field.name" :key="field.name" :icon="field.icon" tabs="gui-form-create" :title="field.label">
                    <AppFormJS v-if="field.children" :id="`${field.name}_appForm`" :fields="field.children" @update:model-value="input"/>
                    <p v-else-if="field.name === 'attributs'">
                        <AppSuspense>
                            <AppAttributeCreate :key="changed" :fields-attributs="fieldsAttributs" :my-boolean-family="myBooleanFamily" @data-attribute="inputAttribute"/>
                        </AppSuspense>
                    </p>
                    <p v-else>
                        field.children à définir {{ field.label }}
                    </p>
                </AppTab>
            </AppTabs>
        </div>
    </AppSuspense>
</template>

<style scoped>
.display-block-important {
    display: block !important;
}
</style>
