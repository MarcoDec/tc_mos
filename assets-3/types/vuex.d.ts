declare module 'vuex' {
    type ActionContext = {commit: Commit, state: State}

    type Actions = {fetchApi: (ctx: ActionContext) => Promise<void>}

    type Commit = ObjectToIntersection<MutationsToCommit>

    type Mutations = {
        error: (state: State) => void
        responseError: (state: State, payload: {status: number, text: string | null}) => void
        spin: (state: State) => void
    }

    type MutationsKey = keyof Mutations

    type MutationsToCommit = {
        [K in MutationsKey]: Payload<K> extends never
            ? (type: K) => void
            : (type: K, payload: Payload<K>) => void
    }

    type Payload<K extends MutationsKey> = Parameters<Mutations[K]> extends [State, infer P] ? P : never

    type State = {
        [key: string]: unknown
        spinner: boolean
        status: number
        text: string | null
    }

    class Store implements StoreProperties {
        public readonly state: State
    }

    type StoreProperties = {
        readonly state: State
    }

    type StoreOptions = StoreProperties & {
        readonly actions: Actions
        readonly mutations: Mutations
        readonly strict: boolean
    }

    function createStore(options: StoreOptions): Store
}
