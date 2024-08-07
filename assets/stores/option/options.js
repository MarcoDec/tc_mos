import api from '../../api'
import {defineStore} from 'pinia'
import useOption from './option'

function sort(a, b) {
    if (typeof a.text === 'undefined' || typeof b.text === 'undefined') return 0
    return a.text.localeCompare(b.text)
}

export default function useOptions(base, valueProp = '@id') {
    const id = `options/${base}`
    return defineStore(id, {
        actions: {
            dispose() {
                for (const option of this.options)
                    if (typeof option.dispose === 'function')
                        option.$dispose()
                this.$dispose()
                this.isLoaded = false
            },
            async fetch() {
                if (!this.fetchable || this.isLoaded)
                    return
                this.isLoaded = false
                const response = await api(this.url)
                this.resetItems()
                for (const option of response['hydra:member']) {
                    this.options.push(useOption(option, this))
                    this.items.push(option)
                }
                this.options.sort(sort)
                this.isLoaded = true
                this.fetchable = false
            },
            async fetchOp() {
                if (this.isLoaded === false) {
                    const response = await api(this.url)
                    this.resetItems()
                    for (const option of response['hydra:member']) {
                        this.options.push(useOption(option, this))
                        this.items.push(option)
                    }
                    this.isLoaded = true
                    this.options.sort(sort)
                }
            },
            resetItems() {
                const options = [...this.options]
                this.options = []
                this.items = []
                this.isLoaded = false
                for (const option of options)
                    option.$dispose()
            },

            // fonctions utiles pour récupération des labels des options
            getLabelFromCode(value) {
                let option = this.options.find(item => item.code === value)
                if (option) {
                    return option.text
                }
                option = this.options.find(item => item.value === value)
                if (option) {
                    return option.text
                }
                return this.getLabelFromValue(value)
            },
            getLabelFromValue(value) {
                return this.options.find(option => option.value === value)?.text ?? value
            },
            getOptionsMap() {
                return this.options.map(op => {
                    const text = op.text
                    const value = op.value
                    const code = op.code
                    return {text, value, code}
                })
            }
        },
        getters: {
            big(state) {
                return !this.hasGroups && state.options.length > 30
            },
            find: state => value => state.options.find(option => option.value === value),

            groups: state => {
                if (!state.options.every(option => Boolean(option.group)))
                    return []
                const groups = {}
                for (const option of state.options) {
                    if (typeof groups[option.group] === 'undefined')
                        groups[option.group] = []
                    groups[option.group].push(option)
                }
                return Object.entries(groups).map(([group, options]) => ({label: group, options})).reverse()
            },
            hasGroups() {
                return this.groups.length > 0
            },
            label() {
                return value => this.find(value)?.text ?? null
            },
            url: state => `/api/${state.base}/options`,
            hasOptions(state){
                return state.options.length > 0
            }
        },
        state: () => ({
            base,
            fetchable: false,
            id, isLoaded: false,
            options: [],
            valueProp,
            items: []
        })
    })()
}
