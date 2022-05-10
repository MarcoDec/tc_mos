<script setup>
    import {assign, createMachine} from 'xstate'
    import AppAlert from '../../components/AppAlert'
    import AppCard from '../../components/AppCard'
    import AppForm from '../../components/form/AppForm'
    import AppOverlay from '../../components/AppOverlay'
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
                        actions: [assign((context, {error}) => ({error}))],
                        target: 'error'
                    },
                    success: {target: 'logged'}
                }
            },
            logged: {type: 'final'}
        }
    }))

    async function submit(data) {
        send('submit')
        const response = await fetch('/api/login', {
            body: JSON.stringify(Object.fromEntries(data)),
            headers: {
                Accept: 'application/ld+json',
                'Content-Type': 'application/json'
            },
            method: 'POST'
        })
        if (response.status !== 200) {
            const error = await response.json()
            send('fail', {error})
        }
    }
</script>

<template>
    <AppOverlay :id="route.name" :spinner="state.matches('loading')" class="row">
        <AppCard class="col" title="Connexion">
            <AppAlert v-if="state.matches('error')">
                {{ state.context.error }}
            </AppAlert>
            <AppForm :id="form" :disabled="state.matches('loading')" :fields="fields" @submit="submit"/>
        </AppCard>
    </AppOverlay>
</template>
