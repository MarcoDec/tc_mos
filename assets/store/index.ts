import type {State as UserState} from './security'
import type {State as CurrencyState} from './management/currency'
import {createStore} from 'vuex'
import {module as users} from './security'
import {module as currency} from './management/currency'

export type RootState = {
    user: UserState;
    currency: CurrencyState;
}

const store = createStore<RootState>({modules: {users,currency}, strict: process.env.NODE_ENV !== 'production'})

export default store
