<script setup>
    import AppFormJS from '../../../components/form/AppFormJS.js'
    import AppTab from '../../../components/tab/AppTab.vue'
    import AppTabs from '../../../components/tab/AppTabs.vue'
    import {computed} from 'vue-demi'

    const fields = computed(() => [
        {
            active: true,
            children: [
                {
                    children: [
                        {label: 'Désignation ', name: 'designation'},
                        {
                            label: 'Famille',
                            name: 'famille'
                        },
                        {label: 'Unité', name: 'unite'},
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
                {label: 'Fournisseur ', name: 'gestion'},
                {label: 'Référence Fournisseur', name: 'refFournisseur'},
                {label: 'Incoterms   ', name: 'incoterms'},
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
                        {label: 'Famille', name: 'famille'},
                        {label: 'Unité', name: 'unite'},
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
            icon: 'puzzle-piece',
            label: 'Attributs',
            mode: 'tab',
            name: 'attributs'
        }
    ])
    let formData = {};
    function input(value) {
        console.log('value', value)
    }
</script>

<template>
    <div class="gui-card">
        <AppTabs id="gui-form-create" style="display: block !important;">
            <AppTab v-for="field in fields" :id="field.name" :key="field.name" :icon="field.icon" tabs="gui-form-create" :title="field.label">
                <AppFormJS v-if="field.children" :id="`${field.name}_appForm`" :fields="field.children"  @update:model-value="input"/>
                <p v-else>
                    {{ field.label }} à définir
                </p>
            </AppTab>
        </AppTabs>
    </div>
</template>
