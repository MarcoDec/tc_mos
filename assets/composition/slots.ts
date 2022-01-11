import type {Slot, VNode} from 'vue'
import type {FunContext} from '../types/vue'

export function useSlots(context: FunContext): VNode[][] {
    const children: VNode[][] = []
    for (const slot of Object.values(context.slots))
        if (typeof slot !== 'undefined')
            children.push((slot as Slot)())
    return children
}
