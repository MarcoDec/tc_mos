<script setup>
    import {defineEmits, defineProps} from 'vue'
    const props = defineProps({
        fields: {required: true, type: Array},
        item: {required: true, type: Object}
    })

    const emit = defineEmits(['ajoute', 'update'])

    function ajoute(){
        emit('ajoute')
    }
    function update(){
        emit('update', props.item)
    }
</script>

<template>
    <td>
        <button v-if="item.ajout" class="btn btn-icon btn-primary btn-sm mx-2" @click="ajoute">
            <Fa icon="pencil-alt"/>
        </button>
        <button v-if="item.update" class="btn btn-icon btn-secondary btn-sm mx-2" @click="update">
            <Fa icon="eye"/>
        </button>
        <button v-if="item.deletable" class="btn btn-danger btn-icon btn-sm mx-2">
            <Fa icon="trash"/>
        </button>
    </td>
    <td v-for="field in fields" :key="field.name">
        {{ item[field.name] }}
    </td>
</template>
