import {createStore} from 'vuex'

const store = createStore<unknown>({strict: process.env.NODE_ENV !== 'production'})

export default store
