<template>
    <v-container>
        <v-layout justify-center align-content-center>
            <v-flex md3>
                <v-card>
                    <v-card-text>
                            <h4 class="headline">Register</h4>
                    </v-card-text>
                    <v-card-text>
                        <v-form ref="registerform">

                            <v-text-field
                            v-model="input.fullname"
                            :rules="rules.fullname"
                            label="Fullname"
                            ></v-text-field>

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

                            <v-btn :loading="loadingSubmit" color="primary" @click="registerUser">
                                Register
                            </v-btn>

                            <v-btn color="primary" to="/login" flat>
                                Login
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
            fullname: ""
        },
        rules: {
            fullname: [
                v => !!v || 'Fullname is required'
            ],
            username: [
                v => !!v || 'Username is required'
            ],
            password: [
                v => !!v || 'Password is required'
            ]
        }
    } },
    methods: {
        registerUser() {
            const e = this;
            const input = Object.assign({}, e.input)
            if( e.$refs.registerform.validate() ) {
                e.loadingSubmit = true
                HTTP.post("/auth/register", input)
                .then(( response ) => {
                    const session = response.data;
                    delete session.response
                    localStorage.setItem("userToken", JSON.stringify( session ));
                    window.location.href = "/"
                })
                .catch(( error ) => {
                    if( error.response ) {
                        if( error.response.status == 422 ) {
                            e.openAlert({ message: "Sorry, we did not recognize that user", color: "error" })
                        } else {
                            e.openAlert({ message: "Could not connect to the server please try again", color: "error" })
                        }
                    }
                })
                .then(() => e.loadingSubmit = false)
            } else {
                e.openAlert({ message: "Please check the error fields", color: "error" })
            }
        },
        openAlert( param ) {
            this.alert = {
                open: true,
                message: param.message,
                color: param.color,
                key: Date.now()
            }
        }
    },
}
</script>