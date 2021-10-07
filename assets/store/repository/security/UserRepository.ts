import type {ComputedRef} from 'vue'
import {computed} from 'vue'
import {useStore} from 'vuex'

const user = 'user'

export const connected: ComputedRef<boolean> = computed(() => useStore().hasModule(user))
