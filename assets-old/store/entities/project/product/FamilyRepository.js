import {AbstractFamilyRepository, ProductFamily} from '../../../modules'

export default class FamilyRepository extends AbstractFamilyRepository {
    nodeRelation = 'productFamily'
    route = '/api/product-families'
    use = ProductFamily
}
