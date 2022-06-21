<script setup>
    import {NotificationRepository} from '../../../store/modules'
    import {computed} from 'vue'
    import {useRepo} from '../../../composition'

    const props = defineProps({notification: {required: true, type: Object}})
    const bg = computed(() => `bg-${props.notification.read ? 'secondary' : 'gray-800'}`)
    const repo = useRepo(NotificationRepository)

    async function read() {
        await repo.read(props.notification)
    }

    async function remove() {
        await repo.remove(props.notification.id)
    } 
</script>

<template>
    <div :class="bg" class="d-flex flex-column mb-2">
        <div>
            <AppBtn v-if="!notification.read" icon="eye" title="Marquer comme lu" @click="read"/>
            <AppBtn icon="trash" title="Supprimer" variant="danger" @click="remove"/>
        </div>
        <span>{{ notification.formattedCreatedAt }}</span>
        <span> {{ notification.subject }}</span>
    </div>
</template>
