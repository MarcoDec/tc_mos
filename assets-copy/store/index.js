import VuexORM from '@vuex-orm/core'
import {createStore} from 'vuex'

export default createStore({
    plugins: [VuexORM.install()],
    strict: process.env.NODE_ENV !== 'production'
})
