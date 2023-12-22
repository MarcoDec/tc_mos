<script setup>
    import {defineEmits, ref} from 'vue'

    const emits = defineEmits(['nextStep'])
    const originGP = ref(true)
    function validate() {
        emits('nextStep', {originGP: originGP.value})
    }
</script>

<template>
    <div class="d-flex flex-row justify-content-around">
        <div>
            <input id="origin_gp" v-model="originGP" type="checkbox"/>
            <label for="origin_gp"><span v-if="originGP">GP</span><span v-else>Antenne</span></label>
        </div>
        <button class="btn btn-success height-80 m-2" @click="validate">
            <Fa :brand="false" icon="chevron-right"/>
        </button>
    </div>
</template>

<style scoped>

</style>