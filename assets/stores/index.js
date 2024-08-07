import {cloneDeep} from 'lodash'
import {createPinia} from 'pinia'

const pinia = createPinia()
pinia.use(({store}) => {
    if (store.setup) {
        const state = cloneDeep(store.$state)
        store.$reset = () => store.$patch(cloneDeep(state))
    }

    const dispose = store.$dispose
    store.$dispose = () => {
        const id = store.$id
        dispose()
        delete pinia.state.value[id]
    }
})

export default pinia
