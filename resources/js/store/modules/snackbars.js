import { snackbar_status, snackbar_icon } from "./../../constants";

const state = {
    snackbar: {
        showing: false,
        title: "",
        text: "",
        color: "success",
        icon: "mdi-checkbox-blank-circle",
        title: "SAMPLE"
    },
    request: {
        message: "",
        status: "",
        title: "",
        type: ""
    }
};

const getters = {
    snackbar: state => state.snackbar,
    request: state => state.request
};

const actions = {
    setSnackbar({ commit }, snackbar) {
        commit("SET_SNACKBAR", {
            showing: true,
            title: snackbar.title,
            text: snackbar.message,
            color: snackbar_status[snackbar.type],
            icon: snackbar_icon[snackbar.type]
        });
    },
    unsetSnackbar({ commit }) {
        commit("UNSET_SNACKBAR");
    }
};

const mutations = {
    SET_SNACKBAR(state, snackbar) {
        state.snackbar = snackbar;
    },
    SET_SNACKBAR2(state, snackbar) {
        state.snackbar.showing = true;
        state.snackbar.text = snackbar.message;
        state.snackbar.color = snackbar_status[snackbar.type];
        state.snackbar.icon = snackbar_icon[snackbar.type];
    },
    UNSET_SNACKBAR(state) {
        state.snackbar.showing = false;
        state.snackbar.text = "";
        state.snackbar.color = "#FFFFFF";
        state.snackbar.icon = "mdi-checkbox-blank-circle";
    },
    SNACKBAR_STATUS(state, response) {
        state.request.type = response.type;
        state.request.status = response.status;
        state.request.title = response.title;
        state.request.message = response.message;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
