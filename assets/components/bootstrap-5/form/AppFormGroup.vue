<script lang="ts" setup>
    import {findField, findFieldGetter} from '../../../store/bootstrap-5/Field'
    import {defineProps} from 'vue'

    const props = defineProps<{field: string}>()
    const {id} = findFieldGetter(props.field)
    const {form, label, name, type} = findField(props.field)
</script>

<template>
    <AppRow css-class="mb-3">
        <AppLabel :for="id">
            {{ label }}
        </AppLabel>
        <AppCol>
            <AppInput :id="id" :form="form" :name="name" :type="type"/>
        </AppCol>
    </AppRow>
</template>
