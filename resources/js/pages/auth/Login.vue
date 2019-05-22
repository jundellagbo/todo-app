<template>
    <v-container>
        <v-layout justify-center align-content-center>
            <v-flex md3>
                <v-card>
                    <v-card-text>
                            <h4 class="headline">Login</h4>
                    </v-card-text>
                    <v-card-text>
                        <v-form ref="loginform">
                            <v-text-field
                            v-model="input.username"
                            :rules="rules.username"
                            label="Username"
                            ></v-text-field>

                            <v-text-field
                            v-model="input.password"
                            :rules="rules.password"
                            label="Password"
                            type="password"
                            ></v-text-field>

                            <v-checkbox
                                :input-value="input.remember"
                                color="primary"
                                label="Remember Me"
                                hide-details
                                @change="input.remember = !input.remember"
                                style="margin-bottom: 20px;"
                            ></v-checkbox>

                            <v-btn :loading="loadingSubmit" @click="login" color="primary">
                                Login
                            </v-btn>

                            <v-btn color="primary" to="/register" flat>
                                Register
                            </v-btn>

                        </v-form>
                    </v-card-text>
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

        </v-layout>
    </v-container>
</template>

<script>
import { HTTP } from "./../../config"
export default {
    data() { return {
        loadingSubmit: false,
        alert: {
            open: false,
            message: "",
            color: "",
            key: Date.now()
        },
        input: {
            username: "",
            password: "",
            remember: false
        },
        rules: {
            username: [
                v => !!v || 'Username is required'
            ],
            password: [
                v => !!v || 'Password is required'
            ]
        }
    } },
    mounted() {
        const rememberMe = localStorage.getItem("remember")
        if( rememberMe ) {
            this.input = JSON.parse( rememberMe );
        }
    },
    methods: {
        openAlert( param ) {
            this.alert = {
                open: true,
                message: param.message,
                color: param.color,
                key: Date.now()
            }
        },
        login() {
            const e = this;
            if( e.$refs.loginform.validate() ) {
                e.loadingSubmit = true
                const input = Object.assign({}, e.input)
                HTTP.post("/auth/login", input)
                .then(( response ) => {
                    const session = response.data;
                    delete session.response
                    if( input.remember ) {
                        localStorage.setItem("remember", JSON.stringify( input ))
                    } else {
                        localStorage.removeItem("remember");
                    }
                    localStorage.setItem("userToken", JSON.stringify( session ));
                    window.location.href = "/"
                })
                .catch(( error ) => {
                    if( error.response ) {
                        if( error.response.status == 401 ) {
                            e.openAlert({ message: "Invalid Login, please try again", color: "error" })
                        } else {
                            e.openAlert({ message: "Could not connect to the server please try again", color: "error" })
                        }
                    }
                })
                .then(() => e.loadingSubmit = false)
            } else {
                e.openAlert({ message: "Please check the error fields", color: "error" })
            }
        }
    },
}
</script>