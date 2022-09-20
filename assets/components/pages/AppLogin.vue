<script setup>
    import AppForm from '../form/AppForm.vue'
    import {useRoute} from 'vue-router'

    const fields = [
        {label: 'Identifiant', name: 'username'},
        {label: 'Mot de passe', name: 'password', type: 'password'}
    ]
    const route = useRoute()
    const form = `${route.name}-form`
</script>

<template>
    <AppForm :id="form" :fields="fields" submit-label="Connexion"/>
</template>
