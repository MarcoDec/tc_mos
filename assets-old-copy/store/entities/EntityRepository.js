import {Entity, FiniteStateMachineRepository, Repository} from '../modules'
import fetchApi from '../../api'

export default class EntityRepository extends Repository {
    use = Entity

    get stateMachineRepo() {
        return this.repo(FiniteStateMachineRepository)
    }

    error(vue, error, status) {
        this.stateMachineRepo.error(vue, error, status)
    }

    finish(vue) {
        this.stateMachineRepo.reset(vue)
    }

    async fetch(vue, url, method, body) {
        try {
            const response = await fetchApi(url, method, body)
            return response
        } catch (e) {
            if (e instanceof Response) {
                const error = await e.json()
                this.error(vue, error, e.status)
            } else
                this.error(vue, 'Une erreur s\'est produite.', 500)
            throw e
        }
    }

    loading(vue) {
        this.stateMachineRepo.load(vue)
    }
}
