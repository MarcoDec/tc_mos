<script setup>
    const props = defineProps({
        fields: {required: true, type: Array},
        item: {required: true, type: Object}
    })
    const emit = defineEmits(['deleted', 'update'])
    function update(){
        emit('update', props.item)
    }
    function deleted(){
        const id = Number(props.item['@id'].match(/\d+/)[0])
        emit('deleted', id)
    }
    function isObject(val) {
        if (val === null) {
            return false
        }
        return typeof val === 'function' || typeof val === 'object'
    }
</script>

<template>
    <td>
        <button class="btn btn-icon btn-secondary btn-sm mx-2" @click="update">
            <Fa icon="eye"/>
        </button>
        <button class="btn btn-danger btn-icon btn-sm mx-2" @click="deleted">
            <Fa icon="trash"/>
        </button>
    </td>
    <td v-for="field in fields" :key="field.name">
        <template v-if="item[field.name] !== null">
            <template v-if="field.type === 'select'">
                <span v-if="field.options.label(item[field.name]) !== null">{{ field.options.label(item[field.name]) }}
                </span>
                <span v-else>
                    {{ item[field.name] }}
                </span>
            </template>
            <template v-else>
                <span v-if="isObject(item[field.name])" class="bg-danger text-white">Object given for field '{{ field.name }}' - {{ item[field.name] }}</span>
                <span v-else>{{ item[field.name] }}</span>
            </template>
        </template>
    </td>
</template>
