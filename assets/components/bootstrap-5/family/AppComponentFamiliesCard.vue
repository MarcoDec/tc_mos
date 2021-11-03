<script lang="ts" setup>
    function doSomething(): boolean {
        // value = true || false.
        console.log('salut')
    }
    function btnretour(): boolean{
        console.log('retour')
    }
</script>

<template>
    <AppCard class="bg-blue">
        <div class="row">
            <div class="col-7">
                <div class="row">
                    <div class="col-2">
                        <button id="btnretour" @click="btnretour">
                            <Fa id="iconretour" icon="angle-double-left"/>
                        </button>
                    </div>
                    <div class="col-10">
                        <h5>
                            <Fa icon="pencil-alt"/>
                            <span> Cables</span>
                        </h5>
                    </div>
                </div>

                <div class="row">
                    <div class="col-4">
                        <label for="">Code</label>
                    </div>
                    <div class="col-8">
                        <input id="input" type="text" class="form-control"/>
                    </div>
                </div>

                <div class="row">
                    <div class="col-4">
                        <label for="">Nom*</label>
                    </div>
                    <div class="col-8">
                        <input id="input" type="text" class="form-control"/>
                    </div>
                </div>

                <div class="row">
                    <div class="col-4">
                        <label for="">Gestion du cuivre</label>
                    </div>
                    <div class="col-8">
                        <VueToggle id="btoggle" name="VueToggle" @toggle="doSomething"/>
                    </div>
                </div>

                <div class="row">
                    <div class="col-4">
                        <label for="">Code douanier</label>
                    </div>
                    <div class="col-8">
                        <input id="input" type="text" class="form-control"/>
                    </div>
                </div>
                <button id="btnmodifier" type="button" class="btn btn-danger">
                    <Fa icon="trash"/> supprimer
                </button>
                <button id="btnmodifier" type="button" class="btn btn-success">
                    <Fa icon="pencil-alt"/> Modifier
                </button>
            </div>
            <div class="col-5">
                <h5>
                    <span> Attribus</span>
                </h5>
            </div>
        </div>
    </AppCard>
</template>

<style scoped>
.row{
  margin-bottom: 20px;
}
#input{
  margin-left: 1cm;
  border-color:#fafafa ;
  width: 10cm;
  height: 0.8cm;
}
.col-4 {
    width: 7%;
}
#btoggle{
  margin-left: 0.8cm;
}
#btnmodifier {
  float: right;
  margin-right: 17px;
  margin-left: 10px;
}
#btnretour{
    background-color: red;
}
#iconretour{
    color: white;
}
</style>
