<script setup>
    import {computed} from 'vue'

    const props = defineProps({
        notification: {required: true, type: Object},
        send: {required: true, type: Function}
    })
    const bg = computed(() => `bg-${props.notification.read ? 'gray-700' : 'gray-600'}`)

    async function read() {
        props.send('submit')
        await props.notification.reading()
        props.send('success')
    }

    async function remove() {
        props.send('submit')
        await props.notification.remove()
        props.send('success')
    }
</script>

<template>
    <div :class="bg" class="border border-2 border-dark d-flex flex-column mb-2">
        <div>
            <AppBtn v-if="!notification.read" icon="eye" label="Marquer comme lu" @click="read"/>
            <AppBtn icon="trash" label="Supprimer" variant="danger" @click="remove"/>
        </div>
        <span>{{ notification.formattedCreatedAt }}</span>
        <span>{{ notification.subject }}</span>
    </div>
</template>
