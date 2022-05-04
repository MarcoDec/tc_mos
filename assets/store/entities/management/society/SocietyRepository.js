import {EntityRepository, Society} from '../../../modules'

export default class SocietyRepository extends EntityRepository {
    use = Society
    url = '/api/societies'
}
