import {defineStore} from 'pinia'

export default function useOption(option, parent) {
    return defineStore(`${parent.id}/${option.id}`, {
        getters: {value: state => state[state.parentStore.valueProp]},
        state: () => ({...option, parentStore: parent})
    })()
}
