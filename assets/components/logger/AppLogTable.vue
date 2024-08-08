<script setup>
    import {defineProps, ref} from 'vue'
    import api from '../../api'

    const props = defineProps({
        iri: {type: String, required: true}
    })
    console.log('props', props)
    // On récupère les informations de log via l'API /workflows/history en POST et avec iri en paramètre
    // On affiche les informations dans un tableau
    const logs = api('/api/workflows/history', 'POST', {iri: props.iri})
    const results = []
    const showTable = ref(false)
    logs.then(data => {
        // On convertit les timestamps au format `2024-08-08T15:47:01Z` en date
        data.forEach(log => {
            log.timestamp = new Date(log.timestamp)
        })
        // On tri selon le timestamp
        data.sort((a, b) => {
            console.log('a - b', a.timestamp - b.timestamp)
            return b.timestamp - a.timestamp
        })
        results.push(...data)
        showTable.value = true
    })
</script>

<template>
    <div class="table-responsive" style="height: 400px; overflow-y: auto;">
        <table v-if="showTable" class="table table-stripped table-hover table-bordered">
            <thead class="thead-dark" style="position: sticky; top: 0; z-index: 100;">
                <tr>
                    <th colspan="6" class="text-center bg-dark text-white">
                        Historique des modifications
                    </th>
                </tr>
                <tr>
                    <th class="bg-info text-center">
                        Date
                    </th>
                    <th class="bg-info text-center">
                        Utilisateur
                    </th>
                    <th class="bg-info text-center">
                        Commentaire
                    </th>
                    <th class="bg-info text-center">
                        Workflow
                    </th>
                    <th class="bg-info text-center">
                        Etat initial
                    </th>
                    <th class="bg-info text-center">
                        Etat final
                    </th>
                </tr>
            </thead>
            <tbody>
                <template v-if="results.length > 0">
                    <tr v-for="log in results" :key="log.timestamp">
                        <td class="text-center">
                            {{ log.timestamp.toLocaleString() }}
                        </td>
                        <td class="text-center">
                            {{ log.user_id }}
                        </td>
                        <td class="text-center">
                            {{ log.message }}
                        </td>
                        <td class="text-center">
                            {{ log.changes.workflow }}
                        </td>
                        <td class="text-center">
                            {{ log.changes.initialState }}
                        </td>
                        <td class="text-center">
                            {{ log.changes.finalState }}
                        </td>
                    </tr>
                </template>
                <tr v-else>
                    <td colspan="6" class="text-center">
                        Aucune modification
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
