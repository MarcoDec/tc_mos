<script setup>
    import {assign, createMachine} from 'xstate'
    import AppCard from '../AppCard.vue'
    import AppForm from '../form/AppForm.vue'
    import AppOverlay from '../AppOverlay'
    import {useMachine} from '@xstate/vue'
    import {useRoute} from 'vue-router'

    const fields = [
        {label: 'Identifiant', name: 'username'},
        {label: 'Mot de passe', name: 'password', type: 'password'}
    ]
    const route = useRoute()
    const form = `${route.name}-form`
    const {send, state} = useMachine(createMachine({
        context: {error: null},
        id: form,
        initial: 'form',
        states: {
            error: {on: {submit: {target: 'loading'}}},
            form: {on: {submit: {target: 'loading'}}},
            loading: {
                on: {
                    fail: {
                        actions: [assign({error: (context, {error}) => error})],
                        target: 'error'
                    },
                    success: {target: 'logged'}
                }
            },
            logged: {type: 'final'}
        }
    }))

    function submit() {
        send('submit')
    }
</script>

<template>
    <AppOverlay :spinner="state.matches('loading')" class="row">
        <AppCard class="col" title="Connexion">
            <AppForm
                :id="form"
                :disabled="state.matches('loading')"
                :fields="fields"
                submit-label="Connexion"
                @submit="submit"/>
        </AppCard>
    </AppOverlay>
</template>
