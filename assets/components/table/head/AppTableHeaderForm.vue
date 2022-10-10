<script setup>
    import AppTableFormField from '../AppTableFormField.vue'
    import {computed} from 'vue'

    const props = defineProps({
        fields: {required: true, type: Array},
        icon: {required: true, type: String},
        id: {required: true, type: String},
        label: {required: true, type: String},
        store: {required: true, type: Object},
        submit: {required: true, type: Function}
    })
    const form = computed(() => `${props.id}-form`)
</script>

<template>
    <tr :id="id">
        <td class="text-center">
            <Fa :icon="icon"/>
        </td>
        <td>
            <AppForm :id="form" class="d-inline m-0 p-0" @submit="submit">
                <AppBtn :icon="icon" :label="label" type="submit" variant="secondary"/>
            </AppForm>
            <slot/>
        </td>
        <AppTableFormField v-for="field in fields" :key="field.name" :field="field" :form="form" :store="store"/>
    </tr>
</template>
