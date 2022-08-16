<script setup>
    import AppManufacturingTableItem from './AppManufacturingTableItem.vue'
    import {defineProps} from 'vue'

    const props = defineProps({
        fields: {required: true, type: Array},
        form: {required: true, type: String},
        id: {required: true, type: String},
        items: {required: true, type: Array},
        title: {required: true, type: String}
    })
    const lengthTable = props.fields.length - 1
</script>

<template>
    <tbody>
        <AppManufacturingTableItem v-for="item in items" :id="id" :key="item.id" :item="item" :fields="fields" :title="title" :form="form"/>
        <tr v-if="title === 'collapse new Ofs'">
            <td :colspan="lengthTable" class="bg-white text-center text-white"/>
            <td>
                <AppBtn type="submit" variant="success">
                    Générer OFs
                </AppBtn>
            </td>
        </tr>
        <tr v-else-if="title === 'collapse ofs ToConfirm'">
            <td :colspan="lengthTable" class="bg-white text-center text-white"/>
            <td>
                <AppBtn type="submit" variant="success">
                    Confirmer OFs
                </AppBtn>
            </td>
        </tr>
    </tbody>
</template>
