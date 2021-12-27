import type {State as UserState} from './security'
import {createStore} from 'vuex'
import {gui} from './gui'
import type {State as guiState} from './gui'
import {users} from './security'

export type RootState = {
    gui: guiState
    user: UserState
}

const store = createStore<RootState>({modules: {users}, strict: process.env.NODE_ENV !== 'production'})

export default store
