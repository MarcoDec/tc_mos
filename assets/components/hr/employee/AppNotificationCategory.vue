<script setup>
    import AppNotification from './AppNotification.vue'
    import {NotificationRepository} from '../../../store/modules'
    import {computed} from 'vue'
    import {useRepo} from '../../../composition'

    const props = defineProps({category: {required: true, type: String}, notifications: {required: true, type: Object}})
    const content = computed(() => `nav-notifications-${props.category}`)
    const target = computed(() => `#${content.value}`)
    const repo = useRepo(NotificationRepository)

    async function read() {
        await repo.readCategory(props.category)
    }

    async function remove() {
        await repo.removeCategory(props.category)
    }
</script>

<template>
    <li class="accordion-item">
        <h6 class="accordion-header">
            <button
                :aria-controls="content"
                :data-bs-target="target"
                aria-expanded="false"
                class="accordion-button collapsed"
                data-bs-toggle="collapse"
                type="button">
                {{ category }}&nbsp;({{ notifications.length }})
            </button>
        </h6>
        <div :id="content" class="accordion-collapse collapse">
            <div class="accordion-body">
                <div>
                    <AppBtn icon="eye" title="Marquer comme lu" @click="read"/>
                    <AppBtn icon="trash" title="Supprimer" variant="danger" @click="remove"/>
                </div>
                <AppNotification
                    v-for="notification in notifications"
                    :key="notification.id"
                    :notification="notification"/>
            </div>
        </div>
    </li>
</template>
