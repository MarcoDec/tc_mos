import {defineStore} from 'pinia'

export default function useAttribute(attribute, parent) {
    return defineStore(`${parent.$id}/${attribute.id}`, {
        actions: {
            dispose() {
                this.parent.removeAttribute(this)
                this.parent = null
                this.attribute = null
                this.$dispose()
            },
            update(attributes, family) {
                if (this.families.includes(family) && !attributes.includes(this['@id']))
                    this.families = this.families.filter(item => item !== family)
                else if (!this.families.includes(family) && attributes.includes(this['@id']))
                    this.families.push(family)
            }
        },
        getters: {
            field: state => ({label: state.name, name: state['@id'], type: 'boolean'}),
            includes: state => family => state.families.includes(family['@id'])
        },
        state: () => ({...attribute, parent})
    })()
}
