<script setup>
    import AppNotification from './AppNotification.vue'
    import {NotificationRepository} from '../../../store/modules'
    import {computed} from 'vue'
    import {useRepo} from '../../../composition'

    const props = defineProps({category: {required: true, type: String}, notifications: {required: true, type: Object}})
    const content = computed(() => `nav-notifications-${props.category}`)
    const target = computed(() => `#${content.value}`)
    const repo = useRepo(NotificationRepository)
    const length = computed(() => props.notifications.filter(notification => !notification.read).length)
    const hasUnread = computed(() => length.value > 0)
    const variant = computed(() => (hasUnread.value ? 'danger' : 'dark'))

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
                {{ category }}
                <AppBadge :variant="variant" no-absolute tooltip>
                    {{ length }}
                </AppBadge>
                <AppBtn v-if="hasUnread" icon="eye" title="Marquer comme lu" @click.prevent="read"/>
                <AppBtn icon="trash" title="Supprimer" variant="danger" @click.prevent="remove"/>
            </button>
        </h6>
        <div :id="content" class="accordion-collapse collapse">
            <div class="accordion-body">
                <AppNotification
                    v-for="notification in notifications"
                    :key="notification.id"
                    :notification="notification"/>
            </div>
        </div>
    </li>
</template>
