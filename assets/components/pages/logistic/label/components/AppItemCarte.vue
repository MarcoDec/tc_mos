<script setup>
    import {defineProps} from 'vue'

    defineProps({
        label: {default: '<label>', required: true, type: String},
        value: {default: '<value>', required: false, type: String}
    })
</script>

<template>
    <li>
        <div class="label">
            {{ label }}
        </div>
        <div class="value">
            <slot>
                {{ value }}
            </slot>
        </div>
    </li>
</template>

<style scoped>
    .label, .value {
        flex-basis: 50%;
    }
    .label {
        font-weight: bold;
        padding-right: 10px; /* Ajoute un peu d'espace entre l'étiquette et la valeur */
    }
    .value {
        text-align: left; /* Aligner à gauche pour la cohérence */
    }
</style>
