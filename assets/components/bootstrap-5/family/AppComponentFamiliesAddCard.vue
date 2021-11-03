<script lang="ts" setup>
    function doSomething(): boolean {
        // value = true || false.
        console.log('salut')
    }
</script>

<template>
    <AppCard class="bg-blue col">
        <div class="row">
            <h6>
                <Fa icon="plus"/>
                <span id="nameInterface"> Ajouter une famille</span>
            </h6>
        </div>
        <div class="row">
            <div class="col-4">
                <label>Parent</label>
            </div>
            <div class="col-8">
                <select id="inputGroupSelect01" class="custom-select">
                    <option selected/>
                    <option value="1">
                        One
                    </option>
                    <option value="2">
                        Two
                    </option>
                    <option value="3">
                        Three
                    </option>
                </select>
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

        <button id="btnajout" type="button" class="btn btn-success">
            <Fa icon="plus"/> Ajouter
        </button>
    </AppCard>
</template>

<style scoped>
.row{
  margin-bottom: 20px;
}
#inputGroupSelect01{
  margin-left: 1cm;
  border-color:#fafafa ;
  width: 13cm;
}
#input{
  margin-left: 1cm;
  border-color:#fafafa ;
  width: 13cm;
}
.col-4 {
    width: 5%;
}
#btoggle{
  margin-left: 0.8cm;
}
#btnajout{
  float: right;
}
</style>
