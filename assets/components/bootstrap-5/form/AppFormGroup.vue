<script lang="ts" setup>
    import type Field from '../../../store/entity/bootstrap-5/form/Field'
    import {defineProps} from 'vue'

    defineProps<{field: Field}>()
</script>

<template>
    <AppRow class="mb-3">
        <AppLabel>{{ field.label }}</AppLabel>
        <AppCol>
            <AppInput :form="field.form" :name="field.name" :type="field.type"/>
        </AppCol>
    </AppRow>
</template>
