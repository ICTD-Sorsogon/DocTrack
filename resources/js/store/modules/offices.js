const state = {
    offices: [],
}

const getters = {
    offices: state => state.offices,
    offices_loading: state => state.office_list_loading,
}

const actions = {
    async getOffices({ commit }) {
        const response = await axios.get('/api/office_list');
        commit('GET_ALL_OFFICES', response.data);
    },
    async createNewOffice({ commit }, form) {
        await axios.post('/api/add_new_office', form)
        .then(response => {
            let res = {
                status: 'success',
                message: `${form.name} was successfully added!`
            }
            commit('SNACKBAR_STATUS', res)
        })
        .catch(error => {
            let res = {
                status: 'failed',
                message: 'The server replied with an error! Please Contact your administrator.'
            }
            commit('SNACKBAR_STATUS', res)
        });
    },
    async updateExistingOffice({ commit }, form) {
        await axios.post('/api/update_existing_office', form)
        .then(response => {
            let res = {
                status: 'success',
                message: `${form.name} was successfully updated!`
            }
            commit('SNACKBAR_STATUS', res)
        })
        .catch(error => {
            let res = {
                status: 'failed',
                message: 'The server replied with an error! Please Contact your administrator.'
            }
            commit('SNACKBAR_STATUS', res)
        });
    },
    async deleteOffice({ commit }, id) {
        await axios.post(`/api/delete_office/${id}`)
        .then(response => {
            let res = {
                status: 'success',
                message: `${response.data[0].name} \nwas successfully deleted!`
            }
            commit('SNACKBAR_STATUS', res)
        })
        .catch(error => {
            let res = {
                status: 'failed',
                message: 'The server replied with an error! Please Contact your administrator.'
            }
            commit('SNACKBAR_STATUS', res)
        });
    },
}

const mutations = {
    GET_ALL_OFFICES (state, offices) {
        state.offices = offices;
    },
    EDIT_OFFICE () {
    },
}

export default {
    state,
    getters,
    actions,
    mutations
};