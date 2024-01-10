<script setup>
    import AppTablePageSuspense from './AppTablePageSuspense.vue'

    defineProps({
        apiBaseRoute: {default: '', required: true, type: String},
        apiTypedRoutes: {
            default: () => {
                const obj = {}
                return obj
            },
            type: Object
        },
        disableRemove: {type: Boolean},
        fields: {required: true, type: Array},
        icon: {required: true, type: String},
        sort: {required: true, type: Object},
        title: {required: true, type: String}
    })

    function input(type, field, store) {
        const option = field.findOption(type)
        if (option !== null)
            store.id = option.iri
    }

    async function onSubmit(fields, send, store, submit) {
        send('submit')
        const option = fields.findOption('@type', null)
        if (option !== null && store.id === option.iri)
            send('fail', {violations: [{message: 'Cette valeur ne doit pas être vide.', propertyPath: '@type'}]})
        else
            await submit()
    }
</script>

<template>
    <AppTablePageSuspense
        :api-base-route="apiBaseRoute"
        :api-typed-routes="apiTypedRoutes"
        :disable-remove="disableRemove"
        :fields="fields" :icon="icon"
        :sort="sort"
        :title="title">
        <template #create(btn)="{fields: createFields, form, icon: submitIcon, label, send, store, submit, variant}">
            <AppForm :id="form" class="d-inline m-0 p-0" @submit="() => onSubmit(createFields, send, store, submit)">
                <AppBtn :icon="submitIcon" :label="label" :variant="variant" type="submit"/>
            </AppForm>
        </template>
        <template #create(@type)="{css, field, form, id, store}">
            <AppInputGuesser
                :id="id"
                :class="css"
                :field="field"
                :form="form"
                @update:model-value="v => input(v, field, store)"/>
        </template>
        <template #search(@type)="{field, form, id, store}">
            <AppInputGuesser :id="id" :field="field" :form="form" @update:model-value="v => input(v, field, store)"/>
        </template>
    </AppTablePageSuspense>
</template>
