import Dashboard from './components/user/Dashboard';
import DocumentRecords from './components/user/DocumentRecords';
import AccountSettings from './components/user/AccountSettings';
import UserManagement from './components/user/UserManagement';

import AllDocument from './components/user/components/AllDocument'
import NewDocument from './components/user/components/NewDocument';
import EditDocument from './components/user/components/EditDocument';
import ReceiveDocument from './components/user/components/ReceiveDocument'
import DocumentAction from './components/user/DocumentAction';
import ReportArchiveList from './components/user/ReportArchiveList';
import ReportAging from './components/user/ReportAging';
import ReportLog from './components/user/ReportLog';
import ReportMasterList from './components/user/ReportMasterList';
import ReportOfficeList from './components/user/ReportOfficeList';

import Login from './components/Login';
import HomeContainer from './components/HomeContainer';
import NotFound from './components/NotFound';

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
            beforeEnter: (to, from, next) => {
                axios.get('api/authenticated').then((response) => {
                    next({name: 'All Active Documents' })
                }).then(()=>{}).catch((error) => {
                    return next()
                });
            },
        },
        {
            path: '/',
            component: HomeContainer,
            requiresAuth: true,
            beforeEnter: (to, from, next) => {
                axios.get('api/authenticated').then((response) => {
                    next()
                }).catch((error) => {
                    return next({name: 'Login' })
                });
            },
            children: [
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
                /**
                 * FIXME: Bug when logging out from these paths, will not redirect to login page instead will redirect
                 * to root path of authenticated user (all_active_document)
                 **/
                {
                    path: 'edit_document/:type/:id?',
                    component: EditDocument,
                    name: 'Edit Document'
                },
                {
                    path: 'all_active_document',
                    component: AllDocument,
                    name: 'All Active Documents',
                },
                {
                    path: 'receive_document/:type/:id?',
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
                // FIXME: Fix bug for submenu when using reports/'pathname'
                {
                    path: 'archive_list',
                    component: ReportArchiveList,
                    name: 'Archive List'
                },
                {
                    path: 'aging',
                    component: ReportAging,
                    name: 'Document Aging Report'
                },
                {
                    path: 'logs',
                    component: ReportLog,
                    name: 'Log Report'
                },
                {
                    path: 'master_list',
                    component: ReportMasterList,
                    name: 'Document Master List'
                },
                {
                    path: 'office_list',
                    component: ReportOfficeList,
                    name: 'Office List'
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