import {h as render} from 'vue'

/**
 * @typedef {{exposedProps?: {[key: string]: string}, name: string, props?: (function(): any), slots?: string[], tag: string}} ComponentBuilderConfig
 */
class ComponentBuilder {
    static variantTypes = ['danger', 'dark', 'info', 'light', 'none', 'primary', 'secondary', 'success', 'warning']

    /**
     * @param config {ComponentBuilderConfig}
     */
    constructor(config) {
        this.config = config
    }

    get exposedProps() {
        const props = {}
        if (typeof this.config.exposedProps === 'object')
            for (const [prop, defaultValue] of Object.entries(this.config.exposedProps))
                if (prop === 'variant')
                    props[prop] = ComponentBuilder.variant(defaultValue)
        return props
    }

    static validateVariant(variant) {
        return ComponentBuilder.variantTypes.includes(variant)
    }

    static variant(defaultValue) {
        return {default: defaultValue, type: String, validator: ComponentBuilder.validateVariant}
    }

    slots(context) {
        const slots = []
        if (Array.isArray(this.config.slots))
            for (const slotName of this.config.slots) {
                const slot = context.slots[slotName]
                if (typeof slot === 'function')
                    slots.push(slot())
            }
        return slots
    }

    render() {
        const fun = (props, context) => render(this.config.tag, this.config.props.call(props), this.slots(context))
        fun.displayName = this.config.name
        fun.props = this.exposedProps
        return fun
    }
}

/**
 * @param config {ComponentBuilderConfig}
 */
export function h(config) {
    return new ComponentBuilder(config)
}

/**
 * @param config {ComponentBuilderConfig}
 */
export function make(config) {
    return h(config).render()
}
