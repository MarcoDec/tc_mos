<script setup>
    import {computed} from 'vue'
    import useNotifications from '../../stores/notification/notifications'

    const props = defineProps({notification: {required: true, type: Object}})
    const bg = computed(
        () => `bg-${props.notification.read ? 'secondary' : 'gray-800'}`
    )
    async function read() {
        const notifications = useNotifications()
        await notifications.read(props.notification.id)
    }

    async function remove() {
        const notifications = useNotifications()
        await notifications.remove(props.notification.id)
    }
</script>

<template>
    <div :class="bg" class="d-flex flex-column mb-2">
        <div>
            <AppBtn
                v-if="!notification.read"
                icon="eye"
                title="Marquer comme lu"
                @click="read"/>
            <AppBtn icon="trash" title="Supprimer" variant="danger" @click="remove"/>
        </div>
        <span>{{ notification.createdAt }}</span>
        <span> {{ notification.subject }}</span>
    </div>
</template>
