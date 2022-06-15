import {useRoute, useRouter as useVueRouter} from 'vue-router'
import {useStore} from 'vuex'

export function useRouter() {
    const route = useRoute()
    return {
        id: route.name,
        route,
        router: useVueRouter()
    }
}

export function useRepo(repository) {
    return useStore().$repo(repository)
}

export function useSlots(context) {
    const children = []
    for (const slot of Object.values(context.slots))
        if (typeof slot !== 'undefined')
            children.push(slot())
    return children
}
