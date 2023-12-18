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
                if (data === 'null') return operateur.value = {status: false}
                else operateur.value = {status: true, data: data}
                //console.log('getOperateur fetch then', data, operateur.value)
            })
    }

    async function check() {
        await getOperateur()
        //console.log('check', operateur.value)
        if (operateur.value.status) {
            //console.log('check ok')
            if (operateur.value.data !== null) {
                operateur.value.name = operateur.value.data.prenom + ' ' + operateur.value.data.nom
                return true
            }
        }
        //console.log('check ko')
        operateur.value = {name: '<à définir>'}
        return false
    }

    async function validate() {
        const result = await check()
        if (result) {
            //console.log('validate ok', operateur.value)
            emits('nextStep', operateur.value)
        }
        else alert('Veuillez scanner le badge de l\'opérateur')
    }

    onMounted(() => {
        inputOperateurRef.value.focus()
        inputOperateurRef.value.select()
    })

</script>

<template>
    <div>
        <div class="step-title">Scanner le badge de l'opérateur</div>
        <div class="d-flex flex-row align-items-baseline align-self-stretch">
            <input id="operateur" ref="inputOperateurRef" v-model="operateurField" class="form-control m-2" type="text"/>
            <button class="btn btn-success m-2" @click="validate()">
                <Fa :brand="false" icon="chevron-right"/>
            </button>
        </div>
    </div>
</template>

<style scoped>

</style>