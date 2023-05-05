type Api = {
    context: string
    id: string
    type: string
}

export type EntityResponse = {
    '@context': string
    '@id': string
    '@type': string
}

export default abstract class Entity {
    public readonly api: Api

    protected constructor(entity: EntityResponse) {
        this.api = {
            context: entity['@context'],
            id: entity['@id'],
            type: entity['@type']
        }
    }
}
