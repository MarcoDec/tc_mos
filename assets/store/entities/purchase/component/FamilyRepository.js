import {AbstractFamilyRepository, ComponentFamily} from '../../../modules'

export default class FamilyRepository extends AbstractFamilyRepository {
    nodeRelation = 'componentFamily'
    route = '/api/component-families'
    use = ComponentFamily
}
