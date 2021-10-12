<script lang="ts" setup>
    import {defineProps, inject, withDefaults} from 'vue'
    import {findFields, registerForm} from '../../../store/bootstrap-5/Form'
    import type {Axios} from 'axios'
    import type {Field} from '../../../store/bootstrap-5/Field'

    const props = withDefaults(defineProps<{
        action: string,
        fields: Field[],
        id: string,
        method?: 'delete' | 'get' | 'patch' | 'post'
    }>(), {method: 'post'})
    registerForm(props.id, props.fields)

    const axios = inject<Axios>('axios')
    const storedFields = findFields(props.id)

    function submit(e: Event): void {
        if (e.target instanceof HTMLFormElement) {
            const data: Record<string, unknown> = {}
            new FormData(e.target).forEach((value, key) => {
                data[key] = value
            })
            axios?.request({
                data,
                headers: {
                    Accept: 'application/ld+json',
                    'Content-Type': 'application/json'
                },
                method: props.method,
                url: props.action
            })
        }
    }
</script>

<template>
    <form :id="id" :action="action" autocomplete="off" method="post" @submit.prevent="submit">
        <AppFormGroup v-for="field in storedFields" :key="field" :field="field"/>
        <AppBtn class="float-end" label="Connexion" type="submit"/>
    </form>
</template>
