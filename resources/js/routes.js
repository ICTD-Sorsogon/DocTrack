import Dashboard from './components/user/Dashboard';
import DocumentRecords from './components/user/DocumentRecords';
import AccountSettings from './components/user/AccountSettings';
import UserManagement from './components/user/UserManagement';

import AllDocument from './components/user/components/AllDocument'
import NewDocument from './components/user/components/NewDocument';
import ReceiveDocument from './components/user/components/ReceiveDocument'
import DocumentAction from './components/user/DocumentAction';
import ReportAging from './components/user/ReportAging';
import ReportMasterList from './components/user/ReportMasterList';

import Login from './components/Login';
import HomeContainer from './components/HomeContainer';
import NotFound from './components/NotFound';

// TODO: Fix navigation guards
export default {
    base: '/',
    mode: 'history',
    routes: [
        {
            path: '*',
            component: NotFound,
            name: 'Not Found'
        },
        {
            path: '/',
            component: Login,
            name: 'Login',
        },
        {
            path: '/',
            component: HomeContainer,
            beforeEnter: (to, from, next) => {
                axios.get('/authenticated').then((response) => {
                    next()
                }).catch(() => {
                    return next({ name: 'Login'})
                });
            },
            children: [
                {
                    path: 'dashboard',
                    component: Dashboard,
                    name: 'Dashboard',
                },
                {
                    path: 'document_records',
                    component: DocumentRecords,
                    name: 'Document Records',
                },
                {
                    path: 'account_settings',
                    component: AccountSettings,
                    name: 'Account Settings'
                },
                {
                    path: 'new_document',
                    component: NewDocument,
                    name: 'New Document'
                },
                {
                    path: 'all_active_document',
                    component: AllDocument,
                    name: 'All Active Documents',
                },
                {
                    path: 'receive_document',
                    component: ReceiveDocument,
                    name: 'Receive Document',
                },
                {
                    path: 'forward_document',
                    component: DocumentAction,
                    name: 'Forward Document',
                    props: true,
                },
                {
                    path: 'terminal_document',
                    component: DocumentAction,
                    name: 'Terminal Document'
                },
                {
                    path: 'reports/aging',
                    component: ReportAging,
                    name: 'Document Aging Report'
                },
                {
                    path: 'reports/master_list',
                    component: ReportMasterList,
                    name: 'Document Master List'
                },
                {
                    path: 'user_management',
                    component: UserManagement,
                    name: 'User Management'
                }
            ]
        },

    ]
}