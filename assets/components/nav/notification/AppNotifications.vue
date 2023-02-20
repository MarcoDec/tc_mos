<script setup>
    import AppBtnAlert from './AppBtnAlert.vue'
    import AppDropdown from '../dropdown/AppDropdown.vue'
    import AppNotificationCategory from './AppNotificationCategory.vue'
    import {onUnmounted} from 'vue'
    import {useMachine} from '../../../composable/xstate'
    import useNotifications from '../../../stores/notification/notifications'

    const notifications = useNotifications()
    await notifications.fetch()

    const {send, state} = useMachine({
        id: 'notifications',
        initial: 'form',
        states: {
            form: {on: {submit: {target: 'loading'}}},
            loading: {on: {success: {target: 'form'}}}
        }
    })

    onUnmounted(() => {
        notifications.dispose()
    })
</script>

<template>
    <AppOverlay :spinner="state.matches('loading')">
        <AppBtnAlert v-if="notifications.isEmpty" class="me-2" disabled icon="bell">
            {{ notifications.readLength }}
        </AppBtnAlert>
        <AppDropdown v-else id="notifications" class="me-2">
            <template #toggle="{id}">
                <AppBtnAlert
                    :id="id"
                    :variant="notifications.variant"
                    aria-expanded="false"
                    data-bs-auto-close="outside"
                    data-bs-toggle="dropdown"
                    icon="bell">
                    {{ notifications.readLength }}
                </AppBtnAlert>
            </template>
            <AppNotificationCategory
                v-for="category in notifications.categories"
                :key="category.name"
                :category="category"
                :send="send"/>
        </AppDropdown>
    </AppOverlay>
</template>
