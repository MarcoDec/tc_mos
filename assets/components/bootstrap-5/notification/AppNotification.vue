<script lang="ts" setup>
    import Cookies from "js-cookie";
    import {Actions, ActionTypes} from "../../../store/notifications";
    import type {Getters, State} from '../../../store/notifications/notification'
    import {
      useNamespacedActions,
      useNamespacedGetters,
      useNamespacedMutations,
      useNamespacedState
    } from 'vuex-composition-helpers'
    import {MutationTypes} from '../../../store/notifications/notification/mutation'
    import type {Mutations} from '../../../store/notifications/notification/mutation'
    import {defineProps, onMounted} from 'vue'

    const props = defineProps<{index: string}>()
    const spaced = `notifications/${props.index}`

    const {category, subject, creationDatetime} = useNamespacedState<State>(spaced, ['category', 'subject', 'creationDatetime'])
    const color = useNamespacedGetters<Getters>(spaced, ['color']).color
    const vu = useNamespacedMutations<Mutations>(spaced, [MutationTypes.VU])[MutationTypes.VU]
    const state = useNamespacedState<State>(spaced, ['read']).read
    const {count} = useNamespacedGetters<Getters>('notifications', ['count'])
    const readNotif = useNamespacedActions<Actions>('notifications', [ActionTypes.NOTIF_READ])[ActionTypes.NOTIF_READ]
    const deleteNotif = useNamespacedActions<Actions>('notifications', [ActionTypes.DELETE_NOTIF])[ActionTypes.DELETE_NOTIF]

    console.log('aaaa',spaced)
    async function click(value:string): void{
      Cookies.set('idNotif',value)
     await readNotif({id:value})
        if (!state.value)
            vu()
    }
    async function supprimer(value:string):void {
      await deleteNotif()
    }
</script>

<template>
  <ul class="list-group">
    <li class="list-group-item active">
      <span class="badgeNotif">{{ count }}</span>
      {{ category }}
    </li>
  <div class="notification-ui_dd-content" @click="click(props.index)">

    <div class="notification-list notification-list--unread">
      <Fa class="iconClose" icon="window-close" @click="supprimer(props.index)"/>

      <div class="notification-list_detail">
                <p><b></b> {{ subject }}</p>
                <p><small class="dateNotif">Date: {{ creationDatetime }}</small></p>
            </div>
            <div class="notification-list_feature-img">
                <img src="https://i.imgur.com/AbZqFnR.jpg" alt="Feature image"/>
            </div>
        </div>
    </div>
  </ul>

</template>


<style scoped>
.notification-list {
  background: v-bind('color');
}
.dateNotif{
  font-weight: bold;
}
.iconClose{
  background-color: white;
  color: red;
}

</style>
