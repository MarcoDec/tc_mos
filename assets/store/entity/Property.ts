type DefaultValue = string[] | boolean | number | string | null
export type State = Record<string, DefaultValue>

export default class Property {
    private readonly def: DefaultValue

    public constructor(
        public readonly name: string,
        public readonly type: string = 'string',
        def: DefaultValue = null
    ) {
        switch (this.type) {
            case 'boolean':
                this.def = false
                break
            case 'number':
                this.def = 0
                break
            case 'Field':
            case 'string':
                this.def = null
                break
            default:
                throw Error(`Unsupported type "${type}"`)
        }
        if (def !== null)
            this.def = def
    }

    public get defaultValue(): DefaultValue {
        return this.def
    }
}
