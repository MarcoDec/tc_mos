import api from '../../../../api'
import {defineStore} from 'pinia'

export default function useEngineGroups() {
    return defineStore('engine-groups', {
        actions: {
            dispose() {
                for (const engine of this.tools)
                    if (typeof engine.dispose === 'function')
                        engine.$dispose()
                for (const engine of this.counterParts)
                    if (typeof engine.dispose === 'function')
                        engine.$dispose()
                for (const engine of this.workstations)
                    if (typeof engine.dispose === 'function')
                        engine.$dispose()
                this.$dispose()
            },
            async fetch(url) {
                const response = await api(url)
                const items = []
                for (const item of response['hydra:member'])
                    items.push(item)
                return items
            },
            async fetchAllEngineGroups() {
                this.isLoaded = false
                this.isLoading = true
                await this.fetchToolGroups()
                await this.fetchCounterPartGroups()
                await this.fetchWorkstationGroups()
                this.isLoading = false
                this.isLoaded = true
            },
            async fetchCounterPartGroups() {
                this.counterPartGroups = []
                this.counterPartGroups = await this.fetch('/api/counter-part-groups')
            },
            async fetchToolGroups() {
                this.toolGroups = []
                this.toolGroups = await this.fetch('/api/tool-groups')
            },
            async fetchWorkstationGroups() {
                this.workstationGroups = []
                this.workstationGroups = await this.fetch('/api/workstation-groups')
            }
        },
        getters: {
            engineGroups: state => [...state.toolGroups, ...state.counterPartGroups, ...state.workstationGroups]
        },
        state: () => ({
            counterPartGroups: [],
            isLoaded: false,
            isLoading: false,
            toolGroups: [],
            workstationGroups: []
        })
    })()
}
