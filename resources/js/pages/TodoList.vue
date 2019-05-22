<template>
    <v-container>
        <v-layout align-center justify-center>
            <v-flex md8 align-self-center>
                <v-card>
                    <v-card-title>
                        <h4 style="margin-bottom: 15px;" class="title">Welcome Jundell Agbo</h4>
                        <v-spacer />
                        <v-tooltip slot="append" bottom>
                            <v-btn icon flat color="primary" slot="activator" @click="logout">
                                <v-icon>power_settings_new</v-icon>
                            </v-btn>
                            <span>Logout User</span>
                        </v-tooltip>
                    </v-card-title>
                    <v-card-title>
                        <div class="filterTodo">
                            <v-text-field
                            label="Search active TODO"
                            @input="filterTodoSearch"
                            ></v-text-field>
                        </div>
                        <div class="filterTodo">
                        <v-select
                            :items="filterTodo"
                            @change="TodoFilterByStatus"
                            label="Filter Todo"
                        ></v-select>
                        </div>
                        <v-checkbox
                            color="primary"
                            label="Sort TODO's"
                            hide-details
                            @change="sortTodoFromActive"
                        ></v-checkbox>
                        <v-btn color="primary" @click="todoform = { open: true, 
                        inputs: { id: 0,
                            todo: '',
                            deadline: null,
                            duedate: null,
                            status: 0,
                            index: 0
                        } }" style="margin-top: 20px;">Add TODO</v-btn>
                    </v-card-title>

                    <v-data-table
                        :headers="headers"
                        :items="taskLists"
                        class="elevation-1"
                        :hide-actions="true"
                        :loading="loadingTodos"
                        :no-data-text="'There is no task available'"
                        :custom-filter="customFilter"
                        :search="filters"
                    >
                        <template v-slot:items="props">
                        <td><div class="l_text">{{ props.item.todo }}</div></td>
                        <td><div class="l_text">{{ props.item.deadline | moment("MMMM DD YYYY | hh:mm:ss A") }}</div></td>
                        <td><div class="l_text">{{ props.item.duedate | moment("MMMM DD YYYY | hh:mm:ss A") }}</div></td>
                        <td>
                            <v-checkbox
                                :input-value="props.item.status"
                                color="success"
                                :label="props.item.statusSet ? 'Completed' : 'Active'"
                                hide-details
                                @change="setStatus({ status: props.item.statusSet, index: props.item.index, id: props.item.id})"
                            ></v-checkbox>
                        </td>
                        <td class="text-xs-right">
                            <div class="btn_wrap_">
                                <v-tooltip slot="append" bottom>
                                    <v-btn icon flat color="primary" slot="activator" @click="todoform = { open: true, inputs: Object.assign({}, props.item) }">
                                        <v-icon>edit</v-icon>
                                    </v-btn>
                                    <span>Update TODO</span>
                                </v-tooltip>
                                <v-tooltip slot="append" bottom>
                                    <v-btn @click="confirmDelete({ id: props.item.id, index: props.item.index })" icon flat color="error" slot="activator">
                                        <v-icon>delete</v-icon>
                                    </v-btn>
                                    <span>Delete TODO</span>
                                </v-tooltip>
                            </div>
                        </td>
                        </template>
                    </v-data-table>

                </v-card>
            </v-flex>

            <!-- alert -->
            <v-snackbar
            v-model="alert.open"
            :top="true"
            :color="alert.color"
            :key="alert.key"
            >
            {{ alert.message }}
            <v-btn
                color="white"
                flat
                @click="alert.open = false"
            >
                Close
            </v-btn>
            </v-snackbar>

           <todoform 
           :todoform="todoform"
           v-on:closeform="() => todoform.open = false"
           v-on:alertform="(param) => openAlert( param )"
           v-on:storetodo="(param) => storeTodo(param)"
           :todos="tasks"
           ></todoform>

        </v-layout>
    </v-container>
</template>

<script>
import TodoForm from "./../components/TodoForm.vue"
import {HTTPAuth} from "./../config"
export default {
    components: {
        'todoform': TodoForm
    },
    data() { return { 
        loadingTodos: false,
        datetime: new Date(),
        taskLoading: false,
        tasks: [],
        headers: [
            { text: "TODO's", value: "todo", sortable: false },
            { text: "Deadline | Time", value: "deadline", sortable: false },
            { text: "Due Date | Time", value: "duedate", sortable: false },
            { text: "Status", value: "status", sortable: false },
            { text: "", value: "options", sortable: false }
        ],
        filterTodo: [
            { text: "Active", value: 0 },
            { text: "Completed", value: 1 },
            { text: "---", value: 2 }
        ],
        alert: {
            open: false,
            message: '',
            key: Date.now()
        },
        todoform: {
            open: false,
            inputs: {}
        },
        filters: {
            searchTodo: '',
            filterTodo: '',
            sortTodo: ''
        }
    } },
    computed: {
        taskLists() {
            return this.tasks.map((row, index) => {
                row.index = index;
                row.statusSet = row.status == 1 ? true : false;
                return row;
            })
        }
    },
    mounted() {
        this.getTodos();
    },
    methods: {
        customFilter( items, filters, filter, headers ) {
            const cf = new this.$MultiFilters(items, filters, filter, headers);

            cf.registerFilter('searchTodo', function (toFilter, items) {
                if (toFilter.trim() === '') return items;
                return items.filter(item => {
                    return (item.todo.indexOf(toFilter) != -1) && (item.status == 0);
                });
            });

            cf.registerFilter('filterTodo', function (toFilter, items) {
                if ( toFilter === "" ) return items;
                return items.filter(item => {
                    return item.status == toFilter;
                });
            });

            cf.registerFilter('sortTodo', function (toFilter, items) {
                if( toFilter ) {
                    return items.sort((a, b) => a.status - b.status)
                } else {
                    return items;
                }
            });

            return cf.runFilters();
        },
        filterTodoSearch( val ) {
            this.filters = this.$MultiFilters.updateFilters( this.filters, { searchTodo: val } )
        },
        TodoFilterByStatus( val ) {
            let valInt = val == 2 ? "" : parseInt( val );
            this.filters = this.$MultiFilters.updateFilters(this.filters, { filterTodo: valInt });
        },
        sortTodoFromActive( val ) {
            this.filters = this.$MultiFilters.updateFilters(this.filters, { sortTodo: val });
        },
        getTodos() {
            const e = this;
            e.loadingTodos = true;
            HTTPAuth.get("/todos")
            .then(( response ) => {
                e.tasks = response.data.todos
            })
            .catch(( error ) => {
                e.openAlert({ message: 'Error when fetching todos', color: 'error' })
            })
            .then(() => e.loadingTodos = false)
        },
        openAlert( param ) {
            this.alert = {
                open: true,
                message: param.message,
                color: param.color,
                key: Date.now()
            }
        },
        async confirmDelete( param ) {
            let res = await this.$confirm('Do you really want to remove this TODO?', 
            { 
                title: 'Confirm Delete', 
                buttonTrueText: 'Yes', 
                buttonFalseText: 'No', 
                color: 'warning', 
                icon: 'warning'
            })

            if( res ) {
                this.execDelete( param );
            }
        },
        execDelete( param ) {
            const e = this;
            HTTPAuth.get("/todos/remove/" + param.id )
            .then(( response ) => {
                e.openAlert({ message: 'Todo has been successfully removed', color: 'success' })
                e.tasks.splice( param.index, 1 )
            })
            .catch(() => {
                e.openAlert({ message: 'Error when removing todo', color: 'error' })
            })
        },
        async logout() {
            let res = await this.$confirm('Are you sure you want to logout?', 
            { 
                title: 'Logout Confirm', 
                buttonTrueText: 'Yes', 
                buttonFalseText: 'No', 
                color: 'primary', 
                icon: 'power_settings_new'
            })

            if( res ) {
                this.execLogout();
            }
        },
        execLogout() {
            localStorage.removeItem("userToken");
            window.location.href = "/login";
        },
        storeTodo( param ) {
            const res = Object.assign({}, param)
            if( res.store == "insert" ) {
                this.tasks.push( res.response );
                this.openAlert({ message: 'New Task has been added', color: 'success' })
            } else {
                this.$set( this.tasks, res.response, res.response.index )
                this.openAlert({ message: 'Task has been saved', color: 'success' })
            }
        },
        setStatus( param ) {
            const e = this;
            const status = !param.status ? 1 : 0;
            HTTPAuth.post("/todos/set-status", { id: param.id, status })
            .then(( response ) => {
                e.tasks[param.index].status = !param.status;
                e.openAlert({ message: 'Status has been updated', color: 'success' })
            })
            .catch(( error ) => {
                e.tasks[param.index].status = param.status;
                e.openAlert({ message: 'Error when changing status, please try again', color: 'error' })
            })
        }
    },
}
</script>

<style lang="css" scope>
    .v-input--selection-controls.v-input .v-label {
        font-size: 13px;
    }
    .filterTodo {
        margin-right: 15px; 
        margin-bottom: -20px;
    }
    @media screen and (max-width: 979px) {
        .btn_wrap_ {
            width: 130px;
        }
        .l_text{
            width: 200px;
        }
        .filterTodo {
            margin-right: 0!important;
            width: 100%!important;
        }
    }
</style>