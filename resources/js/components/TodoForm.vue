<template>
    <v-dialog
    v-model="todoform.open"
    max-width="350"
    persistent 
    >
        <v-card>
            <v-card-title class="headline">
                <span v-if="inputs.id != 0">Edit TODO</span>
                <span v-else>New TODO</span>
            </v-card-title>
            <v-card-text>
                <v-form ref="todoform">
                    <v-text-field
                    v-model="inputs.todo"
                    label="TODO"
                    :error-messages="rules.todo"
                    @input="changepicker($event, 'todo')"
                    ></v-text-field>

                    <v-datetime-picker
                    label="Deadline"
                    v-model="inputs.deadline"
                    timePickerFormat="ampm"
                    format="MMM D, YYYY | hh:mm A"
                    :error-messages="rules.deadline"
                    @input="changepicker($event, 'deadline')"
                    okText="Set Deadline Date">
                    </v-datetime-picker>

                    <v-datetime-picker
                    label="Due Date"
                    v-model="inputs.duedate"
                    timePickerFormat="ampm"
                    format="MMM D, YYYY | hh:mm A"
                    :error-messages="rules.duedate"
                    @input="changepicker($event, 'duedate')"
                    okText="Set Due Date">
                    </v-datetime-picker>

                </v-form>
            </v-card-text>
            <v-card-actions>
                <v-spacer />
                <v-btn :loading="loadingSubmit" color="primary" @click="saveTodo">
                    <span v-if="inputs.id != 0">
                        Save Changes
                    </span>
                    <span v-else>
                        Save TODO
                    </span>
                </v-btn>

                <v-btn
                    color="primary"
                    flat
                    @click="closeForm"
                >
                    Cancel
                </v-btn>

            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import {HTTPAuth} from "./../config"
export default {
    data() { return {
        loadingSubmit: false,
        inputs: {},
        rules: {
            todo: "",
            deadline: "",
            duedate: ""
        }
    } },
    props: ["todoform", "todos"],
    watch: {
        todoform( newVal, oldVal ) {
            this.inputs = newVal.inputs
        }
    },
    methods: {
        closeForm() {
            this.resetValidation();
            this.$emit('closeform');
        },
        customValidation() {
            let error = "Please select date and time";
            this.rules.duedate = this.inputs.duedate == null ? error : "";
            this.rules.deadline = this.inputs.deadline == null ? error : "";
            this.rules.todo = this.inputs.todo == "" ? "Please enter todo" : "";
        },
        changepicker(e, picker) {
            if( e != null ) {
                this.rules[picker] = ""
            }
        },
        saveTodo() {
            const e = this;
            if( e.inputs.todo == "" || e.inputs.deadline == null || e.inputs.duedate == null ) {
                e.customValidation();
                this.$emit('alertform', { message: 'Please check the error fields', color: 'error' })
            } else {
                e.saveToApiTodo();
            }
        },
        moment() {
            return moment();
        },
        saveToApiTodo() {
            const e = this;
            let input = Object.assign({}, e.inputs)
            input.duedate = e.$moment( input.duedate ).format("YYYY-MM-DD HH:mm:ss")
            input.deadline = e.$moment( input.deadline ).format("YYYY-MM-DD HH:mm:ss")
            HTTPAuth.post("/todos/store", input)
            .then(( response ) => {
                const retdata = Object.assign({ index: input.index }, response.data.data)
                const storeType = input.id != 0 ? 'edit' : 'insert';
                e.$emit('storetodo', { store: storeType, response: response.data.data })
                e.$emit('closeform');
                e.resetValidation();
            })
            .catch((error) => {
                if( error.response ) {
                    if( error.response.status == 422 ) {
                        e.$emit('alertform', { message: "You are entering existing todo.", color: "error" })
                    } else {
                        e.$emit('alertform', { message: "Connection Error, please try again", color: "error" })
                    }
                }
            })
            .then(() => e.loadingSubmit = false)
        },
        resetValidation() {
            this.rules = {
                todo: "",
                deadline: "",
                duedate: ""
            }
        }
    },
}
</script>