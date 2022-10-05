import {assign, createMachine} from 'xstate'
import {useMachine as useXstateMachine} from '@xstate/vue'

export {assign}

export function useMachine(machine) {
    return useXstateMachine(createMachine({...machine, predictableActionArguments: true}))
}
