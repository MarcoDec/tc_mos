<script setup>
    const props = defineProps({
        fields: {required: true, type: Array},
        item: {required: true, type: Object}
    })
    //console.log(props.fields)
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
                <template v-if="isObject(item[field.name])">
                    <span v-if="field.options.label(item[field.name]['@id']) !== null">{{ field.options.label(item[field.name]['@id']) }}</span>
                    <span v-else>{{ item[field.name] }}</span>
                </template>
                <template v-else>
                    <span v-if="field.options.label(item[field.name]) !== null">{{ field.options.label(item[field.name]) }}</span>
                    <span v-else>{{ item[field.name] }}</span>
                </template>
            </template>
            <template v-else>
                <template v-if="field.type === 'measure'">
                    <div class="text-center">{{ item[field.name].value }} {{ item[field.name].code }}</div>
                </template>
                <template v-else>
                    <span v-if="isObject(item[field.name])" class="bg-danger text-white">Object given for field '{{ field.name }}' - {{ item[field.name] }}</span>
                    <span v-else>
                        <template v-if="field.type === 'date'">
                            {{ item[field.name].substring(0, 10) }}
                        </template>
                        <template v-else>
                            {{ item[field.name] }}
                        </template>
                    </span>
                </template>
            </template>
        </template>
    </td>
</template>
