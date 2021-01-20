
import { snackbar_status, snackbar_icon } from './../../constants'

const state = {
    snackbar: {
        showing: false,
        text: '',
        color: 'success',
        icon: 'mdi-checkbox-blank-circle',
    },
    request : {
        status: '',
        message: '',
    }
}

const getters = {
    snackbar: state => state.snackbar,
    request: state => state.request,
}

const actions = {
    setSnackbar({commit}, snackbar) {
        const type = snackbar.type;
        const hasKey = type in snackbar_status;
        if(hasKey) {
            var color = snackbar_status[type];
            var icon = snackbar_icon[type];
            commit('SET_SNACKBAR',
            {
                showing: true,
                text: snackbar.message,
                color: color,
                icon : icon
            });
        }
        else {
            commit('SET_SNACKBAR',
            {
                showing: true,
                text: `The snackbar does not have, type of: '${type}'`,
                color: snackbar_status.error,
                icon : snackbar_icon.error
            });
        }
    },
    unsetSnackbar({ commit }) {
        commit('UNSET_SNACKBAR');
    }
}

const mutations = {
    SET_SNACKBAR(state, snackbar) {
        state.snackbar = snackbar;
    },
    UNSET_SNACKBAR(state) {
        state.snackbar.showing = false;
        state.snackbar.text = '';
        state.snackbar.color = '#FFFFFF';
        state.snackbar.icon = 'mdi-checkbox-blank-circle';
    },
    SNACKBAR_STATUS(state, response){
        state.request.status = response.status;
        state.request.message = response.message;
    }
}

export default {
    state,
    getters,
    actions,
    mutations
};