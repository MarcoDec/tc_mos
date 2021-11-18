import type {State as UserState} from './security'
import type {State as guiState} from './gui'
import {actions} from './actions'
import {createStore} from 'vuex'
import {module as users} from './security'
import {module as gui} from './gui'

export type RootState = {
    user: UserState;
    gui: guiState;
}

const store = createStore<RootState>({
    actions,
    modules: {users,gui},
    strict: process.env.NODE_ENV !== 'production'
})

export default store
