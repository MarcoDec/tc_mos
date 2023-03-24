<script setup>
    import {defineEmits, defineProps} from 'vue'
    const props = defineProps({
        fields: {required: true, type: Array},
        item: {required: true, type: Object}
    })

    const emit = defineEmits(['deleted', 'update'])

   
    function update(){
        emit('update', props.item)
        console.log('item',props.item);
    }
    function deleted(){
        const id = Number(props.item['@id'].match(/\d+/)[0]);
        emit('deleted', id)
    }
</script>

<template>
    <td>
        <button  class="btn btn-icon btn-secondary btn-sm mx-2" @click="update">
            <Fa icon="eye"/>
        </button>
        <button  class="btn btn-danger btn-icon btn-sm mx-2" @click="deleted">
            <Fa icon="trash"/>
        </button>
    </td>
    <td v-for="field in fields" :key="field.name">
        {{ item[field.name] }}
    </td>
</template>
