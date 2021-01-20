import axios from 'axios';

function buildName(first_name, middle_name, last_name, suffix) {
    var f_name = '', m_name = '',l_name = '',s_name = '';
    f_name = (first_name.trim()).charAt(0).toUpperCase() + (first_name.trim()).slice(1);
    m_name = (middle_name.trim()).charAt(0).toUpperCase() + (middle_name.trim()).slice(1);
    l_name = (last_name.trim()).charAt(0).toUpperCase() + (last_name.trim()).slice(1);
    if(suffix != null && typeof suffix !== 'undefined') {
        s_name = (suffix.trim()).charAt(0).toUpperCase() + (suffix.trim()).slice(1);
    }
    return `${f_name} ${m_name} ${l_name} ${s_name}`;
}

const state = {
    user: {},
    all_users: [],
    all_users_loading: true,
    user_full_name: '',
    form_requests : {
        request_form_type: '',
        request_status: '',
        status_message: '',
    },
    logs: [],
}

const getters = {
    auth_user: state => state.user,
    auth_user_full_name: state => state.user_full_name,
    form_requests_status: state => state.form_requests,
    all_users: state => state.all_users,
    all_users_complete: state => state.all_users_complete,
    logs: state => state.logs,
    is_admin: state => state.user.role_id == 1,
}

const actions = {
    async getAuthUser({ commit }) {
        const response = await axios.get('/api/auth_user');
        commit('SET_AUTH_USER', response.data);
    },
    async removeAuthUser({ commit }) {
        await axios.post('/logout');
        commit('UNSET_AUTH_USER');
    },
    async getAllUsers({ commit }) {
        await axios.get('/api/all_users')
        .then(response => {
            response.data.forEach(element => {
                element.full_name = '';
                element.full_name = buildName(
                    element.first_name,
                    element.middle_name,
                    element.last_name,
                    element.suffix
                );
                if(element.gender==1) {
                    element.gender="Male";
                }
                else
                    element.gender="Female"
                element.is_active = element.is_active ? "Active" : "Inactive"
            });
            commit('FETCH_ALL_USERS', response.data);
        });
    },
    async getAllUsersComplete({ commit }) {
        await axios.get('/api/all_users_complete')
        .then(response => {
            response.data.forEach(element => {
                element.full_name = '';
                element.full_name = buildName(
                    element.first_name,
                    element.middle_name,
                    element.last_name,
                    element.suffix
                );
            });
        });
    },
    async addNewUser({ commit }, form) {
        await axios.post('/api/add_new_user', form)
        .then(response => {
            const data = {
                form_type: 'new_user',
                code: 'SUCCESS',
                message: `${form.username} was successfully added!`,
                response_data: response.data
            }
            console.log(form)
            commit('snackbars/UPDATE_SNACKBAR_MESSAGE_STATUS', data, { root: true })

        })
        .catch(error => {
            const error_data = {
                form_type: 'new_user',
                code: 'FAILED',
                message: `The server replied with an error! Please Contact your administrator.`,
            }
            commit('snackbars/THROW_SNACKBAR_SERVER_ERROR', error_data, { root: true })
        });
    },
    async updateExistingUser({ commit }, form) {
        await axios.post('/api/update_existing_user', form)
        .then(response => {
            const data = {
                form_type: 'update_user',
                code: 'SUCCESS',
                message: `${form.username} was successfully updated!`,
                response_data: response.data
            }
            commit('snackbars/UPDATE_SNACKBAR_MESSAGE_STATUS', data, { root: true })
        })
        .catch(error => {
            const error_data = {
                form_type: 'update_user',
                code: 'FAILED',
                message: `The server replied with an error! Please Contact your administrator.`,
            }
            commit('snackbars/THROW_SNACKBAR_SERVER_ERROR', error_data, { root: true })
        });
    },
    async deleteExistingUser({ commit }, id) {
        await axios.post(`/api/delete_existing_user/${id}`)
        .then(response => {
            const data = {
                form_type: 'delete_user',
                code: 'SUCCESS',
                message: `${response.data[0].username} \nwas successfully deleted!`,
                response_data: response.data
            }
            commit('snackbars/UPDATE_SNACKBAR_MESSAGE_STATUS', data, { root: true })
        })
        .catch(error => {
            console.log(error);
            const error_data = {
                form_type: 'delete_user',
                code: 'FAILED',
                message: `The server replied with an error! Please Contact your administrator.`,
            }
            commit('snackbars/THROW_SNACKBAR_SERVER_ERROR', error_data, { root: true })
        });
    },
    async editUserCredentials({ commit }, updates) {
        const response = await axios.put(`update_user/${updates.id}`, updates.form);
        if(updates.form.form_type == 'account_details') {
            commit('UPDATE_USER_COMPLETE_NAME', {response: response.data, form: updates.form});
        } else if(updates.form.form_type == 'account_username') {
            commit('UPDATE_USERNAME', {response: response.data, form: updates.form});
        } else if(updates.form.form_type == 'account_password') {
            commit('UPDATE_PASSWORD', {response: response.data, form_type: updates.form_type});
        }
    },
    async removeRequestStatus({commit}) {
        commit('UNSET_REQUEST_STATUS');
    },
    async getLogs({ commit }) {
        const response = await axios.get('/api/logs');
        commit('GET_LOGS', response.data);
    },
}

const mutations = {
    SET_AUTH_USER: (state, user) => {
        state.user = user
        // state.user_full_name = buildName(user.first_name, user.middle_name, user.last_name, user.suffix);
        state.username = user.username;
    },
    UNSET_AUTH_USER: (state) => {
        state.user = {};
        state.user_full_name = '';
        state.form_requests.request_form_type = '';
        state.form_requests.request_status = '';
        state.form_requests.status_message = '';
    },
    FETCH_ALL_USERS: (state, users) => {
        state.all_users = users;
    },
    UPDATE_USER_COMPLETE_NAME: (state, data) => {
        if(data.response.code == "SUCCESS") {
            state.first_name = data.form.first_name;
            state.middle_name = data.form.middle_name;
            state.last_name = data.form.last_name;
            state.suffix = data.form.name_suffix;
            state.user_full_name = buildName(
                data.form.first_name,
                data.form.middle_name,
                data.form.last_name,
                data.form.name_suffix
            );
        }
        state.form_requests.request_form_type = data.form.form_type;
        state.form_requests.request_status = data.response.code;
        state.form_requests.status_message = data.response.message;
    },
    UPDATE_USERNAME: (state, data) => {
        if(data.response.code == "SUCCESS") {
            state.user.username = data.form.new_username;
        }
        state.form_requests.request_form_type = data.form.form_type;
        state.form_requests.request_status = data.response.code;
        state.form_requests.status_message = data.response.message;
    },
    UPDATE_PASSWORD: (state, data) => {
        state.form_requests.request_form_type = data.form_type;
        state.form_requests.request_status = data.response.code;
        state.form_requests.status_message = data.response.message;
    },
    UNSET_REQUEST_STATUS: (state) => {
        state.form_requests.request_form_type = '';
        state.form_requests.request_status = '';
        state.form_requests.status_message = '';
    },
    GET_LOGS(state, response) {
        state.logs = response;
    }
}

export default {
    state,
    getters,
    actions,
    mutations
};