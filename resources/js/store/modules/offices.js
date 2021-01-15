const state = {
    offices: [],
}

const getters = {
    offices: state => state.offices,
    offices_loading: state => state.office_list_loading,
    //form_requests: state => state.form_requests,
}

const actions = {
    async getOffices({ commit }) {
        const response = await axios.get('/api/office_list');
        commit('GET_ALL_OFFICES', response.data);
    },
    async createNewOffice({ commit }, form) {
        await axios.post('/api/add_new_office', form)
        .then(response => {
            console.log('RESPONSE OK:', response);

            const data = {
                form_type: 'new_office',
                code: 'SUCCESS',
                message: `${form.office_code} successfully added!`,
                response_data: response.data
            }
            commit('UPDATE_OFFICE_LIST', data);
        })
        .catch(error => {
            console.log('RESPONSE ERROR:',error);

            const error_data = {
                form_type: 'new_office',
                code: 'FAILED',
                message: `The server replied with an error! Please Contact your administrator\nException Type : ${error.response.data.exception}`,
            }
            commit('THROW_SERVER_ERROR', error_data)
        });
    },
    //async resetStatus(state, data)
}

const mutations = {
    GET_ALL_OFFICES (state, offices) {
        state.offices = offices;
    },
    EDIT_OFFICE () {

    },
    UPDATE_OFFICE_LIST(state, data) {
        // TODO: Update documents list and document tracking list
        state.form_requests.request_form_type = data.form_type;
        state.form_requests.request_status = data.code;
        state.form_requests.status_message = data.message;
    },
    THROW_SERVER_ERROR(state, error) {
        state.form_requests.request_form_type = error.form_type;
        state.form_requests.request_status = error.code;
        state.form_requests.status_message = error.message;
    },
}

export default {
    state,
    getters,
    actions,
    mutations
};