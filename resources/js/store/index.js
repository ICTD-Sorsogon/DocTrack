import documents from './modules/documents';
import users from './modules/users';
import snackbars from './modules/snackbars';
import loader from './modules/loader';
import offices from './modules/offices';
import { datastorage } from './modules/datastorage'
import createPersistedState from 'vuex-persistedstate'

const dataState = createPersistedState({
    paths: ['data'],
    storage: window.sessionStorage,
})

export default {
    modules: {
        datastorage,
        users,
        documents,
        offices,
        snackbars,
        loader,
    },
    plugins: [dataState],
};