<script lang="ts" setup>
    import type {Actions, Getters} from '../../../../store/attributs'
    import type {
        Actions as ActionsFamily,
        Getters as GettersFamily
    } from '../../../../store/famillies'
    import type {FormField, FormValue} from '../../../../types/bootstrap-5'
    import {computed, defineEmits, onMounted, reactive, ref} from 'vue'
    import {
        useNamespacedActions,
        useNamespacedGetters
    } from 'vuex-composition-helpers'

    const emit = defineEmits<(e: 'update:modelValue', value: FormValue) => void>()

    const fetchCountry = useNamespacedActions<ActionsFamily>('famillies', [
        'getFamily'
    ]).getFamily
    const findByAttribut = useNamespacedActions<Actions>('attributs', [
        'findByAttribut'
    ]).findByAttribut

    const options = useNamespacedGetters<GettersFamily>('famillies', [
        'options'
    ]).options
    const listFields = useNamespacedGetters<Getters>('attributs', [
        'fields'
    ]).fields

    const val = reactive<{composant: string | null}>({composant: null})

    const fields = computed<FormField[]>(() => [
        {
            active: true,
            children: [
                {
                    children: [
                        {label: 'Désignation ', name: 'designation'},
                        {
                            label: 'Famille',
                            name: 'famille',
                            options: options.value,
                            type: 'select'
                        },
                        {label: 'Unité', name: 'unite', type: 'select'},
                        {label: 'poids (g) ', name: 'code'}
                    ],
                    label: 'General',
                    mode: 'fieldset',
                    name: 'General'
                },
                {
                    children: [
                        {label: 'Fabricant ', name: 'Fabricant '},
                        {label: 'Référence du Fabricant', name: 'refF'}
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
        {
            children: [
                {label: 'Fournisseur ', name: 'gestion', type: 'select'},
                {label: 'Référence Fournisseur', name: 'refFournisseur'},
                {label: 'Incoterms   ', name: 'incoterms', type: 'select'},
                {label: 'Délai de livraison moyen (jour ouvrés) ', name: 'delai'},
                {label: 'Moq ', name: 'moq', type: 'number'},
                {label: 'Quantité par conditionnement ', name: 'qte'},
                {label: 'Type de conditionnement ', name: 'type'}
            ],
            icon: 'puzzle-piece',
            label: 'Fournisseur',
            mode: 'tab',
            name: 'Fournisseur'
        },
        {
            children: [
                {
                    children: [
                        {label: 'Désignation ', name: 'designation'},
                        {label: 'Famille', name: 'famille', type: 'select'},
                        {label: 'Unité', name: 'unite', type: 'select'},
                        {label: 'Poids (g) ', name: 'code'}
                    ],
                    label: 'Prix',
                    mode: 'fieldset',
                    name: 'prix'
                }
            ],
            icon: 'puzzle-piece',
            label: 'Prix',
            mode: 'tab',
            name: 'prix'
        },
        {
            children: listFields.value,
            icon: 'puzzle-piece',
            label: 'Attributs',
            mode: 'tab',
            name: 'attributs'
        }
    ])
    const hasNoAttribute = computed(() => listFields.value.length === 0)

    const fieldPrix: FormField = {
        btn: true,
        children: [
            {label: 'Désignation ', name: 'designation'},
            {label: 'Famille', name: 'famille', type: 'select'},
            {label: 'Unité', name: 'unite', type: 'select'},
            {label: 'Poids (g) ', name: 'code'}
        ],
        label: 'Prix ',
        name: 'prix'
    }

    const fieldsPrix = ref<FormField[][]>([])

    function click(): void {
        const tab = {...fieldPrix}
        tab.label += fieldsPrix.value.push([tab])
    }

    async function input(e: Readonly<Event>): Promise<void> {
        emit('update:modelValue', (e.target as HTMLInputElement).value)
        val.composant = (e.target as HTMLInputElement).value
        await findByAttribut(val.composant)
    }

    onMounted(async () => {
        await fetchCountry()
        click()
    })
</script>

<template>
    <AppForm id="login" :fields="fields" @input="input" @click="click">
        <template v-if="hasNoAttribute" #attributs>
            <AppBadge variant="danger">
                Erreur
            </AppBadge>
        </template>
    </AppForm>
</template>

<style>
.cardOrderSupplier {
  border: 6px solid #1d583d;
}
.overflow-auto {
  overflow: initial !important;
}
</style>
