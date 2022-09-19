import {h} from 'vue'

function AppFormGroup() {
    return h('div', {class: 'mb-3 row'})
}

AppFormGroup.props = {field: {required: true, type: Object}}

export default AppFormGroup
