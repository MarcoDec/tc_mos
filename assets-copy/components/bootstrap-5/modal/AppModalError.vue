<template>
    <div class="modal-mask">
        <div class="modal-wrapper">
            <div class="modal-container">
                <div class="modal-header">
                    <slot name="header">
                        <Fa class="iconErr" icon="times-circle"/>
                    </slot>
                </div>

                <div class="modal-body">
                    <h1> Ooops!</h1>
                    Something went wrong.
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" @click="$emit('close')">
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
  transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
  background-color: #ffc107;
}
.iconErr{
  color: white;
  font-size: 80px;
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
  width: 500px;
  margin: 0px auto;
  background-color: #fff;
  border-radius: 2px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
  transition: all 0.3s ease;
}

.modal-header {
  background: #e70e0ea6;
  padding: 3rem 2rem;
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

.modal-enter-from, .modal-leave-to {
  opacity: 0;
}

.modal-enter-active .modal-container,
.modal-leave-active .modal-container {
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
}
</style>
