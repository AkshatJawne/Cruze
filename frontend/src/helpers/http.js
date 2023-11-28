import axios from 'axios'

const http = () => {
    let options = {
        baseURL: 'https://localhost',
        headers: {}
    }
    // if token exists, add it to the headers
    if (localStorage.getItem('token')) {
        options.headers.Authorization = `Bearer ${localStorage.getItem('token')}`
    }
    // create axios instance with the options
    return axios.create(options)
}

export default http