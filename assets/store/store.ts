import type {ActionContext} from 'vuex'
import FormRepository from './repository/bootstrap-5/form/FormRepository'
import {createStore} from 'vuex'
import {defineMembers} from './module/builder'
import {useActions} from 'vuex-composition-helpers'

type RepositoryPayload = {repo: string, vueComponent: string}
type Repositories = FormRepository | null
type StoreAction = {
    useRepo: (injectee: StoreActionContext, payload: RepositoryPayload) => Repositories
}
export type StoreState = never
export type ModuleActionContext<S> = ActionContext<S, StoreState>
type StoreActionContext = ActionContext<StoreState, StoreState>

const store = createStore<StoreState>({
    actions: {
        async getRepo(injectee: StoreActionContext, payload: RepositoryPayload): Promise<Repositories> {
            switch (payload.repo) {
                case FormRepository.baseNamespace:
                    if (!store.hasModule(FormRepository.baseNamespace))
                        FormRepository.register({
                            namespace: [FormRepository.baseNamespace],
                            vueComponents: [payload.vueComponent]
                        })
                    return new FormRepository()
                default:
                    return null
            }
        },
        async useRepo(injectee: StoreActionContext, payload: RepositoryPayload): Promise<Repositories> {
            const repo: Repositories = await injectee.dispatch('getRepo', payload)
            if (repo !== null)
                defineMembers(repo, payload.repo)
            return repo
        }
    },
    strict: process.env.NODE_ENV !== 'production'
})

export default store

export async function useRepo(payload: RepositoryPayload): Promise<Repositories> {
    const repo = await useActions<StoreAction>(store, ['useRepo']).useRepo(payload)
    return repo
}
