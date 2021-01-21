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
    // all_users_complete: [],
    all_users_loading: true,
    user_full_name: '',
    logs: [],
}

const getters = {
    auth_user: state => state.user,
    auth_user_full_name: state => state.user_full_name,
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
        commit('CLEAR_FORM_REQUEST');
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
                // element.office_name = element.office.name
                element.gender = element.gender ? "Male" : "Female"
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
    async editUserCredentials({ commit }, updates) {
        await axios.put(`api/update_user/${updates.id}`, updates.form)
        .then(response => {
            var snackbar = {
                showing: true,
                text: response.data.message,
                color: '',
                icon: '',
            };
            if (response.data.code == 'SUCCESS') {
                snackbar.color = 'success';
                snackbar.icon = 'mdi-check-bold';
            }else {
                snackbar.color = 'error';
                snackbar.icon = 'mdi-close-thick';
            }
            if(updates.form.form_type == 'account_details') {
                commit('UPDATE_USER_COMPLETE_NAME', {response: response.data, form: updates.form});
            } else if(updates.form.form_type == 'account_username') {
                commit('UPDATE_USERNAME', {response: response.data, form: updates.form});
            } else if(updates.form.form_type == 'account_password') {
                commit('UPDATE_PASSWORD', {response: response.data, form_type: updates.form_type});
            }
            commit('snackbars/SET_SNACKBAR', snackbar);
        });
    },
    async removeRequestStatus({commit}) {
        commit('CLEAR_FORM_REQUEST');
        commit('UNSET_REQUEST_STATUS');
    },
    async getLogs({ commit }) {
        const response = await axios.get('/api/logs');
        commit('GET_LOGS', response.data);
    },

    async updateUsername({ commit }, form) {
        await axios.put('api/update_username', form)
        .then(response => {
            commit('UPDATE_USERNAME', {response: response.data, changes: form});
            commit('SNACKBAR_STATUS', response.data);
        })
        .catch(error => {
            console.log(error.response.data.errors);
            var snackbar_error ={
                title: error.response.data.message,
                type: 'error',
                status: 'error',
                message: error.response.data.errors
            };
            commit('SNACKBAR_STATUS', snackbar_error);
        });
    },

    async updatePassword({ commit }, form) {
        await axios.put('api/update_password', form)
        .then(response => {
            commit('SNACKBAR_STATUS', response.data);
        })
        .catch(error => {
            var snackbar_error ={
                title: error.response.data.message,
                type: 'error',
                status: 'error',
                message: error.response.data.errors
            };
            commit('SNACKBAR_STATUS', snackbar_error);
        });
    }
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

    },
    FETCH_ALL_USERS: (state, users) => {
        state.all_users = users;
    },
    // FETCH_ALL_USERS_COMPLETE: (state, users) => {
    //     state.all_users_complete = users;
    // },
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
    },
    UPDATE_USERNAME: (state, data) => {
        if (data.response.status == 'success') {
            state.user.username = data.changes.new_username;
        }
        // if(data.response.code == "SUCCESS") {
        //     state.user.username = data.form.new_username;
        // }
        // Snackbar data
        // state.form_requests.request_form_type = data.form.form_type;
        // state.form_requests.request_status = data.response.code;
        // state.form_requests.status_message = data.response.message;
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