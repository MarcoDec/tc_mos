<script lang="ts" setup>
    import type {Actions, Getters} from '../../../store/notifications'
    import {onMounted, ref} from 'vue'
    import {
      useNamespacedActions,
      useNamespacedGetters, useNamespacedMutations
    } from 'vuex-composition-helpers'
    import {ActionTypes} from '../../../store/notifications'
    import {Mutations , MutationTypes} from "../../../store/notifications";
    import AppNotification from './AppNotification.vue'

    const hide = ref(false)

    const {count, ids: notifs, cat} = useNamespacedGetters<Getters>('notifications', ['count', 'ids','cat'])
    const fetchNotif = useNamespacedActions<Actions>('notifications', [ActionTypes.FETCH_NOTIF])[ActionTypes.FETCH_NOTIF]
    const list = useNamespacedMutations<Mutations>('notifications', [MutationTypes.LIST])[MutationTypes.LIST]

    function openModal(): void {
        hide.value = true
    }
    onMounted(async () => {
        await fetchNotif()
    })
</script>

<template>
    <div class="dropdown">
        <button class="dropbtn" @click="openModal">
            <Fa class="icon-notif" icon="bell"/>
        </button>
        <div id="navbarSupportedContent" class="collapse drop navbar-collapse">
            <ul class="ml-auto navbar-nav">
                <li class="nav-item notification-ui show">
                    <a
                        id="navbarDropdown" class="dropdown-toggle nav-link notification-ui_icon" href="#" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="unread-notification">{{ count }}</span>
                    </a>
                    <div v-if="hide" class="dropdown-content">
                        <div class="dropdown-menu notification-ui_dd show" aria-labelledby="navbarDropdown">
                            <div class="notification-ui_dd-header">
                                <h3 class="text-center">
                                    Notification
                                </h3>
                            </div>
                            <AppNotification v-for="notif in notifs" :key="notif" :index="notif"/>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>


<style scoped>
.icon-notif {
  color: white;
}
</style>
