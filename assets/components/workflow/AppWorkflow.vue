<script setup>
    import {computed, defineComponent, defineEmits, defineProps, ref} from 'vue'
    import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
    import AppDropdown from './AppDropdown.vue'

    const props = defineProps({
        workflowName: {default: 'Etat', required: true, type: String},
        currentState: {required: true, type: String},
        possibleActions: {default: () => [], required: true, type: Array},
        defaultAction: {default: null, required: true, type: String},
        showStateName: {
            type: Boolean,
            default: true
        }
    })
    // console.log('props', props)
    defineComponent({
        components: {
            FontAwesomeIcon,
            AppDropdown
        }
    })
    const emit = defineEmits(['applyTransition'])
    const currentAction = ref({})
    currentAction.value = props.defaultAction
    const stateIcons = {
        agreed: 'handshake',
        asked: 'question-circle',
        billed: 'file-invoice-dollar',
        blocked: 'ban',
        cart: 'shopping-cart',
        closed: 'times-circle',
        delayed: 'clock',
        delivered: 'truck',
        disabled: 'toggle-off',
        draft: 'pencil-alt',
        enabled: 'toggle-on',
        forecasted: 'chart-line',
        initial: 'star',
        locked: 'lock',
        monthly: 'calendar-alt',
        paid: 'wallet',
        // eslint-disable-next-line camelcase
        partially_delivered: 'truck-loading',
        // eslint-disable-next-line camelcase
        partially_paid: 'money-bill-wave',
        // eslint-disable-next-line camelcase
        partially_received: 'box-open',
        // eslint-disable-next-line camelcase
        ready_to_work: 'tools',
        // eslint-disable-next-line camelcase
        ready_to_send: 'paper-plane',
        received: 'box',
        rejected: 'times',
        // eslint-disable-next-line camelcase
        to_validate: 'check-circle',
        // eslint-disable-next-line camelcase
        under_maintenance: 'wrench',
        warning: 'exclamation-triangle'
    }
    const stateColors = {
        agreed: '#4CAF50',
        asked: '#2196F3',
        billed: '#1976D2',
        blocked: '#F44336',
        cart: '#FF9800',
        closed: '#607D8B',
        delayed: '#FFEB3B',
        delivered: '#388E3C',
        disabled: '#9E9E9E',
        draft: '#29B6F6',
        enabled: '#8BC34A',
        forecasted: '#9C27B0',
        initial: '#BBDEFB',
        locked: '#C62828',
        monthly: '#673AB7',
        paid: '#4CAF50',
        // eslint-disable-next-line camelcase
        partially_delivered: '#808000',
        // eslint-disable-next-line camelcase
        partially_paid: '#CDDC39',
        // eslint-disable-next-line camelcase
        partially_received: '#FF5722',
        // eslint-disable-next-line camelcase
        ready_to_work: '#607D8B',
        // eslint-disable-next-line camelcase
        ready_to_send: '#00BCD4',
        received: '#795548',
        rejected: '#D32F2F',
        // eslint-disable-next-line camelcase
        to_validate: '#FFC107',
        // eslint-disable-next-line camelcase
        under_maintenance: '#FF9800',
        warning: '#FF9800'
    }
    const transitionIcons = {
        accept: 'check',
        bill: 'file-invoice-dollar',
        block: 'ban',
        buy: 'shopping-cart',
        close: 'times-circle',
        create: 'plus-circle',
        delay: 'clock',
        deliver: 'truck',
        disable: 'toggle-off',
        forecast: 'chart-line',
        month: 'calendar-alt',
        lock: 'lock',
        // eslint-disable-next-line camelcase
        partially_deliver: 'truck-loading',
        // eslint-disable-next-line camelcase
        partially_pay: 'money-bill-wave',
        // eslint-disable-next-line camelcase
        partially_receive: 'box-open',
        pay: 'wallet',
        receive: 'box',
        reject: 'times',
        // eslint-disable-next-line camelcase
        submit_validation: 'paper-plane',
        supervise: 'eye',
        // eslint-disable-next-line camelcase
        under_maintenance: 'tools',
        unlock: 'unlock',
        validate: 'check-double'
    }
    const transitionColors = {
        accept: '#4CAF50',
        bill: '#1976D2',
        block: '#F44336',
        buy: '#FF9800',
        close: '#607D8B',
        create: '#2196F3',
        delay: '#FFEB3B',
        deliver: '#388E3C',
        disable: '#9E9E9E',
        forecast: '#9C27B0',
        month: '#673AB7',
        lock: '#C62828',
        // eslint-disable-next-line camelcase
        partially_deliver: '#808000',
        // eslint-disable-next-line camelcase
        partially_pay: '#CDDC39',
        // eslint-disable-next-line camelcase
        partially_receive: '#FF5722',
        pay: '#4CAF50',
        receive: '#795548',
        reject: '#D32F2F',
        // eslint-disable-next-line camelcase
        submit_validation: '#00BCD4',
        supervise: '#607D8B',
        // eslint-disable-next-line camelcase
        under_maintenance: '#FF9800',
        unlock: '#FFC107',
        validate: '#8BC34A'
    }
    /**
     * Retourne l'icône de l'état courant
     * @returns {*}
     */
    function currentStateIcon() {
        // console.log('currentStateIcon', props.currentState, stateIcons[props.currentState])
        return stateIcons[props.currentState]
    }
    function currentStateColor() {
        // console.log('currentStateColor', props.currentState, stateColors[props.currentState])
        return stateColors[props.currentState]
    }
    /**
     * Renvoie le nom de l'état courant
     * @returns {*}
     */
    function currentStateName() {
        // Retourne le nom de l'état courant
        // console.log('currentStateName', props.currentState)
        return props.currentState
    }
    // console.log('currentStateIcon', currentStateIcon())
    const dropdownActions = computed(() => props.possibleActions.map(action => ({
        id: `tr_${action}`,
        name: action,
        icon: transitionIcons[action],
        color: transitionColors[action]
    })))
    function performAction(action) {
        currentAction.value = action
    }
    function launchTransition() {
        const confirmation = window.confirm(`Lancer la transition ${currentAction.value}?`)
        if (confirmation) {
            emit('applyTransition', {transition: currentAction.value, workflowName: props.workflowName})
        }
    }
    function getBackgroundColor(workflowName) {
        const workflowColors = {
            blocker: 'rgba(255,235,59,0.5)',
            closer: 'rgba(255,235,59,0.5)',
            default: 'rgba(41,182,246,0.5)'
        }
        return workflowColors[workflowName] || workflowColors.default
    }
    function getWorkflowTitle(workflowName) {
        const workflowTitles = {
            blocker: 'Bloqueur',
            closer: 'Clôture',
            default: 'Statut'
        }
        return workflowTitles[workflowName] || workflowTitles.default
    }
    const isDisabled = props.possibleActions.length === 0
</script>

<template>
    <div class="workflow-container border-1" :style="{backgroundColor: getBackgroundColor(workflowName)}">
        <div class="workflow-title">
            {{ getWorkflowTitle(workflowName) }}
        </div>
        <div class="workflow-component">
            <span class="current-state" :style="{color: currentStateColor()}">
                <FontAwesomeIcon :icon="currentStateIcon()"/>
                <span v-if="showStateName">{{ currentStateName() }}</span>
            </span>
            <span class="dropdown-container">
                <AppDropdown
                    :actions="dropdownActions"
                    :default-action="defaultAction"
                    @action-selected="performAction"/>
            </span>
            <button :disabled="isDisabled" class="btn btn-success" :title="`Lance la transition ${currentAction}`" @click="launchTransition">
                <FontAwesomeIcon icon="chevron-right"/>
            </button>
        </div>
    </div>
</template>

<style scoped>
.workflow-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    font-size: small;
    padding: 5px;
}
.workflow-title {
    width: 100%;
    padding-left: 5px;
    text-align: left;
    font-weight: bold;
    font-size: xx-small;
}
.workflow-component {
    display: flex;
    align-items: center;
}
.current-state{
    display: flex;
    flex-direction: column;
    background-color: rgba(255, 255, 255, 0.5);
    border-radius: 5px;
    border: 1px solid lightgray;
}
.current-state i, .actions i {
    /* Styles des icônes */
}
.actions button {
    /* Styles des boutons d'action */
}
.dropdown-container {
    max-width: 300px;
    margin-left: 10px;
}
.border-1 {
    //border: 1px solid #000;
    border-radius: 10px;
}
</style>
