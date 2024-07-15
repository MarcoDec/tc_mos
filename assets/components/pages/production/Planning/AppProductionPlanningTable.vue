<script setup>
    import {computed, ref} from 'vue'
    import {useRoute} from 'vue-router'
    import {useProductionPlanningsFieldsStore} from '../../../../stores/productionPlannings/productionPlannings'
    import useUser from "../../../../stores/security"
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome"

    const props = defineProps({
        fields: {required: true, type: Array}
    })
    const route = useRoute()
    const user = useUser()
    const userCompanyId = user.company.id
    const isLoaded = ref(false)
    const storeProductionPlanningsFields = useProductionPlanningsFieldsStore()
    //storeProductionPlanningsFields.fetch()
    storeProductionPlanningsFields.fetch().then(() => {
        isLoaded.value = true
    })

    const combinedFields = computed(() => [...props.fields, ...storeProductionPlanningsFields.fields])
    function downloadCSVFile(csvData) {
        // Create CSV file object and feed
        // our csv_data into it
        const CSVFile = new Blob([csvData], {
            type: "text/csv"
        })

        // Create to temporary link to initiate
        // download process
        const tempLink = document.createElement("a")

        const currentDate = new Date().toJSON().slice(0, 10)
        // Download csv file
        tempLink.download = `newgp_${currentDate}_${userCompanyId}.csv`
        tempLink.href = window.URL.createObjectURL(CSVFile)

        // This link should not be displayed
        tempLink.style.display = "none"
        document.body.appendChild(tempLink)

        // Automatically click the link to
        // trigger download
        tempLink.click()
        document.body.removeChild(tempLink)
    }
    function tabletocsv() {
        // Variable to store the final csv data
        let csvData = []
        // Get each row data
        const rows = document.querySelectorAll(".schedule-table tr")
        for (let i = 0; i < rows.length; i++) {
            // Get each column data
            const cols = rows[i].querySelectorAll("td,th")

            // Stores each csv row data
            const csvRow = []
            for (let j = 0; j < cols.length; j++) {
                // Get the text data of each cell
                // of a row and push it to csvrow
                let item = cols[j].innerHTML
                item = item.replaceAll("&amp;", "&")
                item = item.replaceAll("&nbsp;", " ")
                csvRow.push(item)
            }

            // Combine each column value with comma
            csvData.push(csvRow.join(";"))
        }

        // Combine each row data with new line character
        csvData = csvData.join("\n")

        // Call this function to download csv file
        downloadCSVFile(csvData)
    }
</script>

<template>
    <div v-if="isLoaded" class="tableFixHead">
        <button class="btn btn-success" @click="tabletocsv">
            <FontAwesomeIcon icon="file-csv"/>
            <span>Download CSV</span>
        </button>
        <table :id="route.name" class="schedule-table">
            <thead>
                <tr>
                    <th v-for="field in combinedFields" :key="field.name" :style="{width: field.width}">
                        {{ field.label }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in storeProductionPlanningsFields.items" :key="item.id">
                    <td v-for="field in combinedFields" :key="field.name">
                        {{ item[field.name] }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div v-else class="text-center">
        Loading... Please wait...<br/>
        <span class="spinner-border" role="status"/>
    </div>
</template>

<style scoped>
    .schedule-table {
        border-collapse: collapse;
        border-spacing: 0;
        width: 100%;
    }
    .schedule-table th,
    .schedule-table td {
        border: 1px solid black;
        padding: 8px;
        text-align: left;
    }
    .schedule-table thead th {
        background-color: darkgrey;
        font-weight: bold;
        position: sticky;
        top: 0; /* Fixe l'en-tête en haut du conteneur */
        z-index: 1; /* Assure que l'en-tête reste au-dessus du contenu */
    }
    .tableFixHead {
        overflow: auto;
        height: calc(100vh - 146px); /* Hauteur du conteneur avec défilement */
    }
    button > span {
        font-size:xx-small;
        margin-left:10px;
    }
    button {
        margin-bottom: 5px;
    }
</style>
