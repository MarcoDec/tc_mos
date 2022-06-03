// import {Employee, FiniteStateMachine, Model} from './modules'
import {Model} from './modules'
import {Repository as VuexORMRepository} from '@vuex-orm/core'
// import store from '.'

// const fix = [Employee.entity, FiniteStateMachine.entity]

export default class Repository extends VuexORMRepository {
    use = Model

    get isEmpty() {
        return this.all().length === 0
    }

    destroy(ids, vue) {
        let collection = []
        if (Array.isArray(ids)) {
            for (const id of ids) {
                const removed = this.removeVue(id, vue)
                if (removed !== null)
                    collection.push(removed)
            }
        } else {
            const removed = this.removeVue(ids, vue)
            if (removed !== null)
                collection = removed
        }
        this.unregisterModule(vue)
        return collection
    }

    destroyAll(vue) {
        const all = this.all()
        if (all.length > 0)
            for (const record of all)
                this.destroy(record.id, vue)
        else
            this.unregisterModule(vue)
    }

    pushVue(record, vue) {
        if (!Array.isArray(record.vues))
            record.vues = [...this.find(record.id)?.vues ?? []]
        record.vues.push(vue)
    }

    removeVue(id, removed) {
        const record = this.find(id)
        if (record === null)
            return null
        if (!Array.isArray(record.vues))
            record.vues = []
        record.vues = record.vues.filter(vue => vue !== removed)
        return record.vues.length === 0 ? super.destroy(record.id) : this.save(record)
    }

    reset(id) {
        const old = this.find(id)
        if (old) {
            const record = {}
            for (const [name, field] of Object.entries(this.use.fields()))
                record[name] = field.make()
            this.save({...record, id, vues: old.vues})
        }
    }

    save(records, vue = null) {
        if (vue !== null && typeof records === 'object') {
            if (Array.isArray(records))
                for (const record of records)
                    this.pushVue(record, vue)
            else
                this.pushVue(records, vue)
        }
        return super.save(records)
    }

    unregisterModule() {
        if (this.isEmpty) {
            this.flush()
            /* if (!fix.includes(this.use.entity)) {
                delete store.$database.models[this.use.entity]
                delete store.$database.schemas[this.use.entity]
                store.unregisterModule(['entities', this.use.entity])
            } */
        }
    }
}
