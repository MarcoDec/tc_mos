<script setup>
    import AppTab from '../../../tab/AppTab.vue'
    import AppTabs from '../../../tab/AppTabs.vue'
    import AppTemplateList from './components/AppTemplateList.vue'
</script>

<template>
    <AppTabs id="label-templates">
        <!--        <AppTab id="component-labels" title="Modèles Etiquettes Composant" tabs="label-templates" icon="">-->
        <!--            Hello World-->
        <!--        </AppTab>-->
        <!--        <AppTab id="product-labels" title="Modèles Etiquettes Produit" tabs="label-templates" icon="">-->
        <!--            Hello World-->
        <!--        </AppTab>-->
        <AppTab id="carton-labels" title="Modèles Etiquettes Carton" tabs="label-templates" icon="box-open" active>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-6">
                        <div class="row">
                            <AppTemplateList
                                title="Modèles Etiquettes Carton Format TCONCEPT"
                                label-kind="TConcept"
                                template-familly="carton"/>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="row">
                            <AppTemplateList
                                title="Modèles Etiquettes Carton Format ETI9"
                                label-kind="ETI9"
                                template-familly="carton"/>
                        </div>
                    </div>
                </div>
            </div>
        </AppTab>
        <!--        <AppTab id="pallet-labels" title="Modèles Etiquettes Palette" tabs="label-templates" icon="">-->
        <!--            Hello World-->
        <!--        </AppTab>-->
    </AppTabs>
</template>

<style scoped>
    div.active { position: relative; z-index: 0; overflow: scroll; max-height: 100%}
    .labelClass {
        font-size: 17px;
        font-weight: bold;
        color: darkblue;
        text-align: center;
        min-height: 50px;
        max-height: 50px;
        width: 107px;
    }
    .tc-infos {
        text-align: justify;
        position: relative;
    }
    .tc-icons {
        font-size: 100px;
        color: sandybrown;
        background-color: lightgrey;
        border-radius: 10px;
        padding: 10px;
    }
    .productClass {
        font-size: 12px;
        color: darkblue;
        text-align: center;
        min-height: 20px;
        max-height: 20px;
        width: 107px;
    }
</style>
