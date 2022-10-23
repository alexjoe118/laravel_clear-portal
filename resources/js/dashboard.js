import { setupPage } from './domain/page';
import UserUserEdit from './dashboard/user/user-edit';
import UserDocumentIndex from './dashboard/user/document-index';
import UserLoanIndex from './dashboard/user/loan-index';
import UserLoanProductIndex from './dashboard/user/loan-product-index';
import AdminOpenApprovalForm from './dashboard/admin/open-approval-form';
import AdminLoanForm from './dashboard/admin/loan-form';
import AdminReportIndex from './dashboard/admin/report-index';

// Globals.
import Notifications from './dashboard/global/notifications';
import Sidebar from './dashboard/global/sidebar';

setupPage({
	'dashboard-user-user-edit': UserUserEdit,
	'dashboard-user-document-index': UserDocumentIndex,
	'dashboard-user-loan-index': UserLoanIndex,
	'dashboard-user-loan-product-index': UserLoanProductIndex,
	'dashboard-admin-open-approval-form': AdminOpenApprovalForm,
	'dashboard-admin-loan-form': AdminLoanForm,
	'dashboard-admin-report-index': AdminReportIndex
});

// Setup Globals.
const dashboard = document.querySelector( '.js-dashboard' );

const globals = {
	'notifications': Notifications,
	'sidebar': Sidebar
};

for ( const globalSlug in globals ) {
	const instance = new globals[ globalSlug ]( dashboard );
	instance.setup();
}
