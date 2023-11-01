import router from "@/router";
import { U } from "af-table-column";
import axios from "axios";
axios.defaults.timeout = 5000;

axios.defaults.baseURL = "http://localhost/public/index.php/manage";

axios.interceptors.response.use(
    response => {
        if (response.status == 200) {
            return Promise.resolve(response);
        }
        else {
            return Promise.reject(response);
        }
    },
    error => {
        if (error.response.status) {
            switch (error.response.status) {
                case 401:
                    router.replace({
                        path: '/',
                        query: {
                            redirect: router.currentRoute.fullPath
                        }
                    })
                    break;
                case 404:
                    break;
            }
            return Promise.reject(error.response);
        }
    }
);

export function get(url, params = {}) {
    return new Promise((resolve, reject) => {
        axios.get(url, { parames: params })
            .then(response => {
                resolve(response.data);
            })
            .catch(err => {
                reject(err);
            })
    })
}

export function post(url, data = {}) {
    return new Promise((resolve, reject) => {
        axios.post(url, data)
            .then(response => {
                resolve(response.data);
            })
            .catch(err => {
                reject(err);
            })
    })
}
export function upload(url, data = {}) {
    return new Promise((resolve, reject) => {
        axios.post(url,data,
            {"Content-Type": "multipart/form-data"}
        ).then(response => {
            resolve(response.data);
        })
        .catch(err => {
            reject(err);
        })
    })
}

