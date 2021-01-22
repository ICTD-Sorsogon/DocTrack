
import { snackbar_status, snackbar_icon } from './../../constants'

function nl2br (str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
 }

 const state = {
    snackbar: {
        showing: false,
        title: '',
        text: '',
        color: 'success',
        icon: 'mdi-checkbox-blank-circle',
        title: "SAMPLE"
    },
    request : {
        message: '',
        status: '',
        title: '',
        type: ''
    }
}

const getters = {
    snackbar: state => state.snackbar,
    request: state => state.request,
}

const actions = {
    setSnackbar({commit}, snackbar) {
        if (snackbar.constructor.toString().indexOf("Object") != -1) {
            //const required = {message: '', status: '', title: '', type: ''};
            //if (JSON.stringify(Object.keys(snackbar)) === JSON.stringify(Object.keys(required))) {
            if (snackbar.hasOwnProperty('type')? true : false && snackbar.hasOwnProperty('message')? true : false) {
                var color = snackbar_status[snackbar.type];
                var icon = snackbar_icon[snackbar.type];
                commit('SET_SNACKBAR',
                {
                    showing: true,
                    title: snackbar.title,
                    text: snackbar.message,
                    color: color,
                    icon : icon
                });
            } else {
                console.log('setSnackbar');
                commit('SET_SNACKBAR',
                {
                    showing: true,
                    text: `The snackbar does not have, typeof required Object key { type:'', message:'' }`,
                    color: snackbar_status.error,
                    icon : snackbar_icon.error
                });
            }
        } else {
            commit('SET_SNACKBAR',
            {
                showing: true,
                text: `The snackbar payload must be the property of Object`,
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
    SET_SNACKBAR2(state, snackbar) {
        if (snackbar.constructor.toString().indexOf("Object") != -1) {
            //const required = { type: '', message: ''};
            //if (JSON.stringify(Object.keys(snackbar)) === JSON.stringify(Object.keys(required))) {
            if (snackbar.hasOwnProperty('type')? true : false && snackbar.hasOwnProperty('message')? true : false) {
                state.snackbar.showing = true;
                state.snackbar.text = snackbar.message;
                state.snackbar.color = snackbar_status[snackbar.type];
                state.snackbar.icon  = snackbar_icon[snackbar.type];
            } else {
                state.snackbar.showing = true;
                state.snackbar.text = `The snackbar does not have, typeof required Object key { type:'', message:'' }`;
                state.snackbar.color = snackbar_status.error;
                state.snackbar.icon  = snackbar_icon.error;
            }
        } else {
            state.snackbar.showing = true;
            state.snackbar.text = `The snackbar payload must be the property of Object`;
            state.snackbar.color = snackbar_status.error;
            state.snackbar.icon  = snackbar_icon.error;
        }
    },
    UNSET_SNACKBAR(state) {
        state.snackbar.showing = false;
        state.snackbar.text = '';
        state.snackbar.color = '#FFFFFF';
        state.snackbar.icon = 'mdi-checkbox-blank-circle';
    },
    SNACKBAR_STATUS(state, response){
        if (response.constructor.toString().indexOf("Object") != -1) {
            //const required = {message: '', status: '', title: '', type: ''};
            //if (JSON.stringify(Object.keys(response)) === JSON.stringify(Object.keys(required))) {
            if (response.hasOwnProperty('status')? true : false && response.hasOwnProperty('message')? true : false) {
                state.request.type = response.type;
                state.request.status = response.status;
                state.request.title = response.title;
                state.request.message = response.message;
            } else {
                state.snackbar.showing = true;
                state.snackbar.text = `The snackbar request status payload does not have, typeof required Object key { status:'', message:'' }`;
                state.snackbar.color = snackbar_status.error;
                state.snackbar.icon = snackbar_icon.error;
            }
        } else {
            state.snackbar.showing = true;
            state.snackbar.text = `The snackbar request status payload must be the property of Object`;
            state.snackbar.color = snackbar_status.error;
            state.snackbar.icon = snackbar_icon.error;
        }
    }
}

export default {
    state,
    getters,
    actions,
    mutations
};