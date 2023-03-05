<script setup>
    import {defineEmits, defineProps} from 'vue'
    defineProps({
        date: {required: true, type: String},
        id: {required: true, type: Number},
        name: {required: true, type: String},
        relation: {required: true, type: String},
        relationId: {required: true, type: String}
    })
    const emit = defineEmits(['close'])
</script>

<template>
    <div class="modal-mask">
        <div class="modal-wrapper">
            <div class="modal-container">
                <div class="modal-header">
                    <slot name="header">
                        <Fa class="iconErr" icon="calendar-week"/>
                    </slot>
                </div>
                <div class="modal-body">
                    <span>Id d'évenement :{{ id }}</span><br/>
                    <span>Title d'évenement :{{ name }}</span><br/>
                    <span>Date d'évenement :{{ date }}</span>
                    <br/>
                    <span>Relation :{{ relation }}</span>
                    <br/>
                    <span>RelationId :{{ relationId }}</span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" @click="emit('close')">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.btn {
  display: inline-block;
  font-weight: 600;
  line-height: 1.5;
  color: #212529;
  text-align: center;
  text-decoration: none;
  vertical-align: middle;
  cursor: pointer;
  user-select: none;
  background-color: transparent;
  border: 1px solid transparent;
  padding: 8px 25px;
  font-size: 1rem;
  border-radius: 0.25rem;
  transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out,
    border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
  background-color: #ffc107;
}
.iconErr {
  color: white;
  font-size: 50px;
}
.modal-mask {
  position: fixed;
  z-index: 9998;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: table;
  transition: opacity 0.3s ease;
}

.modal-wrapper {
  display: table-cell;
  vertical-align: middle;
}

.modal-container {
  width: 300px;
  margin: 0px auto;
  background-color: #fff;
  border-radius: 2px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
  transition: all 0.3s ease;
}

.modal-header {
  background: #3788d8;
  justify-content: center;
  border-top-right-radius: initial;
}
.modal-header h3 {
  margin-top: 0;
  color: #42b983;
}

.modal-body {
  margin: 10px 0;
  text-align: center;
}
.modal-footer {
  justify-content: center;
  border-top: 0px solid #dee2e6;
}
.modal-default-button {
  float: right;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .modal-container,
.modal-leave-active .modal-container {
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
}
</style>
