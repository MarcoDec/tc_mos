<script lang="ts" setup>
    import {Field} from '../../../store/bootstrap-5/Field'
    import type {UserResponse} from '../../../store/security/User'
    import {connect} from '../../../store/store'
    import router from '../../router'

    const fields: Field[] = [
        new Field('Identifiant', 'username'),
        new Field('Mot de passe', 'password', 'password')
    ]

    function login(user: UserResponse): void {
        connect(user)
        router.push({name: 'home'})
    }
</script>

<template>
    <AppRow>
        <AppCard class="bg-blue col">
            <AppForm id="login" :fields="fields" action="/api/login" @success="login"/>
        </AppCard>
    </AppRow>
</template>
