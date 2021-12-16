<script lang="ts" setup>
    import type {Getters, State} from '../../../store/notifications/notification'
    import {useNamespacedGetters, useNamespacedMutations, useNamespacedState} from 'vuex-composition-helpers'
    import {MutationTypes} from '../../../store/notifications/notification/mutation'
    import type {Mutations} from '../../../store/notifications/notification/mutation'
    import {defineProps} from 'vue'

    const props = defineProps<{index: string}>()
    const spaced = `notifications/${props.index}`

    const {title, details, date} = useNamespacedState<State>(spaced, ['title', 'details', 'date'])
    const color = useNamespacedGetters<Getters>(spaced, ['color']).color
    const vu = useNamespacedMutations<Mutations>(spaced, [MutationTypes.VU])[MutationTypes.VU]
    const state = useNamespacedState<State>(spaced, ['vu']).vu

    function click(): void{
        if (state.value)
            vu()
    }
</script>

<template>
    <div class="notification-ui_dd-content" @click="click">
        <div class="notification-list notification-list--unread">
            <div class="notification-list_detail">
                <p><b>{{ title }}</b> {{ details }}</p>
                <p><small>il y a {{ date }}</small></p>
            </div>
            <div class="notification-list_feature-img">
                <img src="https://i.imgur.com/AbZqFnR.jpg" alt="Feature image"/>
            </div>
        </div>
    </div>
</template>


<style scoped>
.notification-list {
  background: v-bind('color');
}
</style>
