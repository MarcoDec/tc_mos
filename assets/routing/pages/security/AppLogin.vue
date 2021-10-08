<script lang="ts" setup>
    import Field from '../../../store/entity/bootstrap-5/form/Field'
    import {ref} from 'vue'

    const form = ref('login')
    const fields = ref([new Field(form.value, 'username')])
</script>

<template>
    <AppCard>
        <AppForm :id="form" :fields="fields"/>
    </AppCard>
</template>
