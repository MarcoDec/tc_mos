<script setup>
    import {assign, createMachine} from 'xstate'
    import {useRoute, useRouter} from 'vue-router'
    import AppAlert from '../../components/AppAlert'
    import {useMachine} from '@xstate/vue'
    import useUserStore from '../../stores/hr/employee/user'

    const fields = [
        {label: 'Identifiant', name: 'username'},
        {label: 'Mot de passe', name: 'password', type: 'password'}
    ]
    const route = useRoute()
    const router = useRouter()
    const user = useUserStore()
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
        try {
            await user.connect(data)
            send('success')
            await router.push({name: 'home'})
        } catch (error) {
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
            <AppForm
                :id="form"
                :disabled="state.matches('loading')"
                :fields="fields"
                submit-label="Connexion"
                @submit="submit"/>
        </AppCard>
    </AppOverlay>
</template>
