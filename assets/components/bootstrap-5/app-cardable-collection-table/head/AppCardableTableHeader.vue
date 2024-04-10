<script setup>
    import AppCardableTableHeaderfieleds from './AppCardableTableHeaderfieleds.vue'

    const emit = defineEmits(['trierAlphabet'])

    defineProps({
        fields: {required: true, type: Array},
        title: {default: null, required: false, type: String},
        topOffset: {default: '0px', type: String}
    })
    function trierAlphabet(payload) {
        emit('trierAlphabet', payload)
    }
</script>

<template>
    <thead class="table-dark" :style="{ position: 'sticky', top: topOffset }">
        <tr v-if="title !== null">
            <td class="bg-secondary text-uppercase text-xl-center" :colspan="fields.length + 1">
                <slot name="title">
                    {{ title }}
                </slot>
            </td>
        </tr>
        <tr>
            <th scope="col">
                Actions
            </th>
            <AppCardableTableHeaderfieleds :fields="fields" @trier-alphabet="trierAlphabet"/>
        </tr>
        <slot name="form"></slot>
    </thead>
</template>

<style scoped>
    thead::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border-color: white;
        background-color: white; /* Ou toute autre couleur de fond souhaitée */
        z-index: -1;
    }
    thead {
        background-color: rgba(255, 255, 255, 1);
        box-shadow: 0 2px 2px -1px white;
        border-color: white;
    }
</style>
