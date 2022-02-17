import type {State as Items} from '..'

type State = {
        isLoaded: boolean
        items?: Items
    }
   export const state :State = {
    isLoaded : false
    }
