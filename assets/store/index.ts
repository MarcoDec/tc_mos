import type {State as UserState} from './security'
import type {State as cardState} from './gui/card'
import type {State as guiState} from './gui'

import {createStore} from 'vuex'
import {module as card} from './gui/card'
import {module as gui} from './gui'
import {module as users} from './security'

export type RootState = {
    user: UserState;
    gui: guiState;
    card: cardState;
}


const store = createStore<RootState>({modules: {users, gui, card}, strict: process.env.NODE_ENV !== 'production'})

export default store
