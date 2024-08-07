import {get, set} from 'lodash'
import api from '../../api'
import {defineStore} from 'pinia'

export default function useRow(row, table) {
    let initialState = {...row}
    return defineStore(`${table.id}/${row.id}`, {
        actions: {
            dispose() {
                this.table.removeRow(this)
                this.$dispose()
            },
            initUpdate(fields) {
                this.updated = {}
                for (const field of fields.fields) {
                    set(this.updated, field.name, get(this, field.name))
                }
            },
            async remove() {
                await api(this.url, 'DELETE')
                this.dispose()
            },
            async update() {
                // console.info('update', this.table.fields, this)
                for (const field of this.table.fields) {
                    if (field.type === 'multiselect-fetch') {
                        // console.info('field', field)
                        const currentValue = get(this.updated, field.name)
                        // console.info( 'valeur', currentValue)
                        // console.info(currentValue,currentValue.length > 0)
                        if (field.max === 1 && typeof currentValue === 'object' && currentValue.length > 0) {
                            // console.info('update this.updated')
                            set(this.updated, field.name, get(this.updated, field.name)[0])
                            // console.info('updated', this.updated)
                        }
                    }
                }
                initialState = await api(this.url, 'PATCH', this.updated)
                this.$reset()
            }
        },
        getters: {
            url: state => `${state.table.baseUrl}/${state.id}`
        },
        state: () => ({...initialState, table, updated: {}})
    })()
}
