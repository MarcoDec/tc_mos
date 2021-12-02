import type {State as UserState} from './security'
import {createStore} from 'vuex'
import {module as gui} from './gui'
import type {State as guiState} from './gui'
import {module as users} from './security'

export type RootState = {
    gui: guiState
    user: UserState
}

const store = createStore<RootState>({modules: {gui, users}, strict: process.env.NODE_ENV !== 'production'})

export default store
