import axios from "axios"

const baseURL = "http://127.0.0.1:8000/api";

const session = localStorage.getItem("userToken");
export const user = session ? JSON.parse(session) : null;

export const HTTP = axios.create({
    baseURL,
    headers: {
        "Accept": "application/json"
    },
    timeout: 5000
})

const token = user != null ? user.token : "";

export const HTTPAuth = axios.create({
    baseURL,
    headers: {
        "Authorization": "Bearer " + token,
        "Accept": "application/json"
    },
    timeout: 5000
})