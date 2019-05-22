import Vue from "vue"
import VueRouter from "vue-router"

import TodoList from "./pages/TodoList.vue"
import Login from "./pages/auth/Login.vue"
import Register from "./pages/auth/Register.vue"
import NotFound from "./pages/errors/NotFound"

Vue.use( VueRouter )

const routes = [
    { path: '/', component: TodoList, meta: { requiresAuth: true } },
    { path: '/login', component: Login },
    { path: '/register', component: Register },
    { path: '*', component: NotFound }
]

export default routes