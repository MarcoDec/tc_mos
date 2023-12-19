<script setup>
    import {defineEmits, onMounted, ref} from 'vue'
    const emits = defineEmits(['nextStep'])

    const operateurField = ref('')
    const operateur = ref({})
    const inputOperateurRef = ref(null)
    operateur.value = {name: '<à définir>'}

    async function getOperateur() {
        // On tente de récupérer l'Opérateur depuis les informations du code barre
        // en regardant si l'Opérateur est définit coté GP (Antenne inutile car clone de ce coté là)
        // la route de récupération coté gp est:
        // http://gp.tconcept.local/dist/api/operateur.php?action=show&id=3
        await fetch(`http://gp.tconcept.local/dist/api/operateur.php?action=show&id=${operateurField.value}`)
            .then(response => response.json())
            .then(data => {
                if (data === 'null') operateur.value = {status: false}
                else operateur.value = {status: true, data}
            })
    }

    async function check() {
        await getOperateur()
        if (operateur.value.status) {
            if (operateur.value.data !== null) {
                operateur.value.name = `${operateur.value.data.prenom} ${operateur.value.data.nom}`
                return true
            }
        }
        operateur.value = {name: '<à définir>'}
        return false
    }

    async function validate() {
        const result = await check()
        if (result) emits('nextStep', operateur.value)
        else alert('Veuillez scanner le badge de l\'opérateur')
    }

    onMounted(() => {
        inputOperateurRef.value.focus()
        inputOperateurRef.value.select()
    })
</script>

<template>
    <div>
        <div class="step-title">
            Scanner le badge de l'opérateur
        </div>
        <div class="align-items-baseline align-self-stretch d-flex flex-row">
            <input id="operateur" ref="inputOperateurRef" v-model="operateurField" class="form-control m-2" type="text"/>
            <button class="btn btn-success m-2" @click="validate">
                <Fa :brand="false" icon="chevron-right"/>
            </button>
        </div>
    </div>
</template>
