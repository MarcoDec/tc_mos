import {Model as VuexORMModel} from '@vuex-orm/core'

export default class Model extends VuexORMModel {
    static entity = 'model'
    static primaryKey = 'id'

    static fields() {
        return {
            id: this.number(0),
            vues: this.attr([])
        }
    }
}
