import {assign, createMachine} from 'xstate'
import {useMachine as useXMachine} from '@xstate/vue'

export function useMachine(id) {
    return useXMachine(createMachine({
        context: {violations: []},
        id,
        initial: 'loading',
        states: {
            error: {on: {submit: {actions: [assign({violations: []})], target: 'loading'}}},
            form: {on: {submit: {actions: [assign({violations: []})], target: 'loading'}}},
            loading: {
                on: {
                    fail: {actions: [assign((context, {violations}) => ({violations}))], target: 'error'},
                    success: {target: 'form'}
                }
            }
        }
    }))
}
