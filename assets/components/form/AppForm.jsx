import AppFormGroup from './field/AppFormGroup'

function AppForm(props) {
    return <form autocomplete="off" enctype="multipart/form-data" method="POST" novalidate>
        {props.fields.map(field => <AppFormGroup field={field}/>)}
    </form>
}

AppForm.props = {fields: {required: true, type: Array}}

export default AppForm
