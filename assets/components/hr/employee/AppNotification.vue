<script setup>
    import {NotificationRepository} from '../../../store/modules'
    import {computed} from 'vue'
    import {useRepo} from '../../../composition'

    const props = defineProps({notification: {required: true, type: Object}})
    const bg = computed(() => ({'bg-secondary': props.notification.read}))
    const repo = useRepo(NotificationRepository)

    async function read() {
        await repo.read(props.notification)
    }

    async function remove() {
        await repo.remove(props.notification.id)
    }
</script>

<template>
    <div :class="bg">
        {{ notification.subject }}
        <AppBtn icon="eye" title="Marquer comme lu" @click="read"/>
        <AppBtn icon="trash" title="Supprimer" variant="danger" @click="remove"/>
    </div>
</template>
