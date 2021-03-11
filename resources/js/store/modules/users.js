import axios from 'axios';
import { snackbar_status, snackbar_icon } from './../../constants';

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
    logs: [],
    notifs: [],
    count_notifs: [],
}

const getters = {
    auth_user: state => state.user,
    auth_user_full_name: state => state.user_full_name,
    all_users: state => state.all_users,
    all_users_complete: state => state.all_users_complete,
    logs: state => state.logs,
    notifs: state => state.notifs,
    is_admin: state => state.user.role_id == 1,
    find_user: ({all_users}) => (id) => all_users.find(user => user.id == id),
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
    async addNewUser({ commit }, form) {
        await axios.post('/api/add_new_user', form)
        .then(response => {
            const data = {
                status: 'SUCCESS',
                message: `${form.username} was successfully added!`,
            }
            commit('SNACKBAR_STATUS', data)

        })
        .catch(error => {
            const error_data = {
                status: 'FAILED',
                message: `The server replied with an error! Please Contact your administrator.`,
            }
            commit('SNACKBAR_STATUS', error_data)
        });
    },
    async updateExistingUser({ commit }, form) {
        await axios.post('/api/update_existing_user', form)
        .then(response => {
            const data = {
                status: 'SUCCESS',
                message: `${form.username} was successfully updated!`,
            }
            commit('SNACKBAR_STATUS', data)
        })
        .catch(error => {
            const error_data = {
                status: 'FAILED',
                message: `The server replied with an error! Please Contact your administrator.`,
            }
            commit('SNACKBAR_STATUS', error_data)
        });
    },
    async deleteExistingUser({ commit }, id) {
        await axios.post(`/api/delete_existing_user/${id}`)
        .then(response => {
            const data = {
                status: 'SUCCESS',
                message: `${response.data[0].username} \nwas successfully deleted!`,
            }
            commit('SNACKBAR_STATUS', data)
        })
        .catch(error => {
            const error_data = {
                status: 'FAILED',
                message: `The server replied with an error! Please Contact your administrator.`,
            }
            commit('SNACKBAR_STATUS', error_data)
        });
    },
    async getLogs({ commit }) {
        const response = await axios.get('/api/logs');
        commit('GET_LOGS', response.data);
    },

    async uploadProfilePicture({ commit }, image) {
        let data = new FormData();
        data.append('name', 'my-picture');
        data.append('file', image);

        let config = {
          header : {
            'Content-Type' : 'image/png'
          }
        }
        await axios.post(`/api/upload_profile_picture`, data, config)
        .then(response => {
            const data = {
                status: 'SUCCESS',
                message: `Avatar successfully uploaded!`,
            }
            commit('SNACKBAR_STATUS', data)
        })
        .catch(error => {
            const error_data = {
                status: 'FAILED',
                message: `The server replied with an error! Please Contact your administrator.`,
            }
            commit('SNACKBAR_STATUS', error_data)
        });
    },

    async updateFullname({ commit }, form) {
        const response = await axios.put('api/update_fullname', form)
        .then(response => {
            commit('UPDATE_USER_COMPLETE_NAME', {response: response.data, changes: form});
            const type = response.data? 'success':'info';
            var color = snackbar_status[type];
            var icon = snackbar_icon[type];
            commit('SET_SNACKBAR',
            {
                showing: true,
                title: type === 'info'? 'Update failed':'Update success',
                text: response.data? 'User fullname updated':'No changes were made',
                color: color,
                icon : icon
            });
        })
        .catch(error => {
            const type = 'error';
            var color = snackbar_status[type];
            var icon = snackbar_icon[type];
            commit('SET_SNACKBAR',
            {
                showing: true,
                title: error.response.data.message,
                text: error.response.data.errors,
                color: color,
                icon : icon
            });
        });
    },

    async updateUsername({ commit }, form) {
        await axios.put('api/update_username', form)
        .then(response => {
            commit('UPDATE_USERNAME', {response: response.data, changes: form});
            commit('SNACKBAR_STATUS', response.data);
        })
        .catch(error => {
            var snackbar_error ={
                message: error.response.data.errors,
                status: 'error',
                title: error.response.data.message,
                type: 'error'
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
                message: error.response.data.errors,
                status: 'error',
                title: error.response.data.message,
                type: 'error'
            };
            commit('SNACKBAR_STATUS', snackbar_error);
        });
    },

    async getNotifs({ commit, state }) {
        await axios.get('/api/notifs')
        .then(response =>{
            commit('GET_NOTIFS', response.data);
        })
        .catch(error => {
            var snackbar_error ={
                message: error.response.data.errors,
                status: 'error',
                title: error.response.data.message,
                type: 'error'
            };
            commit('SNACKBAR_STATUS', snackbar_error);
        });
    },

    async seenNotif({ dispatch, commit }, notif) {
        await axios.put(`/api/notifs/${notif.id}`, notif)
        .then(response => {
            dispatch('getNotifs')
        })
        .catch(error => {
            var snackbar_error ={
                message: error.response.data.errors,
                status: 'error',
                title: error.response.data.message,
                type: 'error'
            };
            commit('SNACKBAR_STATUS', snackbar_error);
        });
    },

    async seenBadge({ dispatch, commit }, badge) {
        await axios.put(`/api/badge`, badge)
        .then(response => {
            dispatch('getNotifs')
        })
        .catch(error => {
            var snackbar_error ={
                message: error.response.data.errors,
                status: 'error',
                title: error.response.data.message,
                type: 'error'
            };
            commit('SNACKBAR_STATUS', snackbar_error);
        });
    },

}

const mutations = {
    SET_AUTH_USER: (state, user) => {
        state.user = user
        state.user_full_name = buildName(user.first_name, user.middle_name, user.last_name, user.suffix);
        state.username = user.username;
    },
    UNSET_AUTH_USER: (state) => {
        state.user = {};
        state.user_full_name = '';

    },
    FETCH_ALL_USERS: (state, users) => {
        state.all_users = users;
    },
    UPDATE_USER_COMPLETE_NAME: (state, data) => {
        if(data.response) {
            state.first_name = data.changes.first_name;
            state.middle_name = data.changes.middle_name;
            state.last_name = data.changes.last_name;
            state.suffix = data.changes.name_suffix;
            state.user_full_name = buildName(
                data.changes.first_name,
                data.changes.middle_name,
                data.changes.last_name,
                data.changes.name_suffix
            );
        }
    },
    UPDATE_USERNAME: (state, data) => {
        state.user.username = data.changes.new_username;

    },
    GET_LOGS(state, response) {
        state.logs = response;
    },
    GET_NOTIFS(state, response) {
        state.notifs = response;
    },
}

export default {
    state,
    getters,
    actions,
    mutations
};