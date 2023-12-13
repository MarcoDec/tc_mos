<script setup>
    import {defineProps} from 'vue'

    const props = defineProps({
        steps: {required: true, type: Array},
        currentStep: {required: true, type: Number}
        // les items steps sont de la forme:
        // {
        //     id: 1,
        //     label: 'Opérateur',
        //     check: function () {return operateur.value !== '<à définir>'},
        //     validate: function () {
        //         if (this.check()) {
        //             console.log('validate', this.check())
        //         }
        //         else alert('Veuillez scanner le badge de l\'opérateur')
        //     }
        // }
    })
    console.log('steps', props.steps)
    console.log('currentStep', props.currentStep)
    function getStepClass(step) {
        if (step.id === props.currentStep) return 'currentStep'
        if (step.id < props.currentStep) return 'previousStep'
        return 'nextStep'
    }
</script>

<template>
    <div class="steps-container">
        <div class="steps">
            <div v-for="(step, index) in steps" :key="`${step}-${index}`" class="step" :class="getStepClass(step)">
                {{ step.label }}
            </div>
        </div>
    </div>
</template>

<style scoped>
    .steps {
         display: flex;
         align-items: center;
         font-size: 8px;
     }
    .step {
        padding: 5px 10px; /* Espacement autour du texte */
        position: relative;
        margin-right: 11px; /* Ajustez en fonction de la taille du chevron */
        //box-shadow: lightgrey 0px 5px 3px 3px;
    }
    .step::after {
        content: '';
        position: absolute;
        right: -11px; /* Ajustez en fonction de la taille du chevron */
        top: 0px;
        border-top: 11px solid transparent; /* La moitié de la hauteur de l'étape */
        border-bottom: 11px solid transparent; /* La moitié de la hauteur de l'étape */
    }
    .currentStep {
        background-color:yellow;
        color: black;
    }
    .currentStep::after {
        border-left: 11px solid yellow; /* Couleur de fond et taille du chevron */
    }
    .nextStep {
        background-color: grey;
        color: white;
    }
    .nextStep::after {
        border-left: 11px solid grey; /* Couleur de fond et taille du chevron */
    }
    .previousStep {
        background-color: green;
        color: white;
    }
    .previousStep::after {
        border-left: 11px solid green; /* Couleur de fond et taille du chevron */
    }
    .steps-container {
        display: flex;
        justify-content: center;
        margin: 5px 0;
    }
</style>
