<script setup>
    import {ref} from 'vue'
    const blabla = ref('Price à faire')
    console.log(blabla.value)
</script>

<template>
    <AppCardShow id="addPrice"/>
</template>

