import type {FunContext} from '../types/vue'
import type {VNode} from 'vue'

export function useSlots(context: FunContext): VNode[][] {
    const children: VNode[][] = []
    for (const slot of Object.values(context.slots))
        if (typeof slot !== 'undefined')
            children.push(slot())
    return children
}
