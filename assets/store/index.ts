import type {State as UserState, UserStore} from './security'
import {createStore} from 'vuex'
import {store as users} from './security'


export type Store = UserStore<Pick<RootState, 'user'>>
export type RootState = {
    user: UserState;

}
const store = createStore({
    modules: {
        users
    },
    strict: process.env.NODE_ENV !== 'production'
})
export default store
