export type DefaultValue = boolean | number | string | null
export type State = Record<string, DefaultValue>
type Type = 'boolean' | 'number' | 'string'

export default class Property {
    private readonly def: DefaultValue

    public constructor(
        public readonly name: string,
        public readonly type: Type = 'string',
        def: DefaultValue = null
    ) {
        if (def !== null)
            this.def = def
        else
            switch (this.type) {
                case 'boolean':
                    this.def = false
                    break
                case 'number':
                    this.def = 0
                    break
                default:
                    this.def = null
            }
    }

    public get defaultValue(): DefaultValue {
        return this.def
    }
}
