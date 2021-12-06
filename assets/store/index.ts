import type {State as componentState} from './purchase/component'
import {createStore} from 'vuex'
import {module as component} from './purchase/component'
import {module as users} from './security'
import type {State as UserState} from './security'

export type RootState = {
    user: UserState;
    component: componentState;
}

const store = createStore<RootState>({modules: {component, users}, strict: process.env.NODE_ENV !== 'production'})

export default store
