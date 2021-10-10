<script lang="ts" setup>
    import {findFields, registerForm} from '../../../store/bootstrap-5/Form'
    import type {Field} from '../../../store/bootstrap-5/Field'
    import {defineProps} from 'vue'

    const props = defineProps<{fields: Field[], id: string}>()
    registerForm(props.id, props.fields)
    const storedFields = findFields(props.id)
</script>

<template>
    <form :id="id" autocomplete="off">
        <AppFormGroup v-for="field in storedFields" :key="field" :field="field"/>
        <AppBtn class="float-end" label="Connexion" type="submit"/>
    </form>
</template>
