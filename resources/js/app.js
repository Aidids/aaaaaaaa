import PrimeVue from "primevue/config";
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'primevue/resources/themes/nova-vue/theme.css';
import 'primevue/resources/primevue.min.css';
import 'primeicons/primeicons.css';
import ToastService from 'primevue/toastservice';
import MultiSelect from "primevue/multiselect";
import Dropdown from 'primevue/dropdown';
import SelectButton from "primevue/selectbutton";
import FileUpload from "primevue/fileupload";
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import { setupCalendar, Calendar } from 'v-calendar';
import 'v-calendar/style.css';
import DepartmentMain from "./components/administration/DepartmentMain.vue";
import LeaveRequestMain from "./components/leave/ApplyLeave.vue";
import LeaveRequestHistory from "./components/leave/History.vue";
import LeaveRequestApprover from "./components/leave/approver/Index.vue";
import LeaveSummary from "./components/leave/summary/LeaveSummary.vue";
import ProfileMain from './components/profile/Index.vue';
import EmployeeMain from './components/employee/Index.vue';
import ApproversMain from './components/administration/ApproversIndex.vue';
import AdministrationMain from './components/administration/Main.vue';
import DashboardMain from './components/dashboard/Index.vue';
import CalendarIndex from './components/dashboard/calendar/Index.vue';
import UpdateLeaveBalance from './components/administration/updateLeaveBalance/UpdateLeaveBalanceIndex.vue';
import DeductLeave from './components/administration/deductLeave/Index.vue';
import CreateLeaveRedemption from "./components/leave/redeem/CreateLeaveRedemption.vue";
import RedemptionHistory from "./components/leave/redeem/Index.vue";
import RedemptionApprove from "./components/leave/redeem/ApproverIndex.vue";
import RedemptionSummary from "./components/leave/redeem/HrIndex.vue";
import EformIndex from "./components/eform/Index.vue";
import EformApply from "./components/eform/Apply.vue";
import EformApprove from "./components/eform/ApproverIndex.vue";
import EformSummary from "./components/eform/HrIndex.vue";
import TEShow from "./components/eform/expenses/TEShow.vue";
import TravelForm from "./components/eform/travel/TAForm.vue";
import TAPrint from "./components/eform/travel/TAPrint.vue";
import TEForm from "./components/eform/expenses/TEForm.vue";
import UserGuide from "./components/UserGuide.vue";

import Tooltip from 'primevue/tooltip';
// TODO: PLEASE DO NOT USE SYSTEM-MSG, USE MSG-MODAL INSTEAD
import SystemMsg from './components/elements/SystemMsg.vue';
import MsgModal from './components/elements/MsgModal.vue';
import ErrorModal from './components/elements/ErrorModal.vue';
import Loader from './components/elements/Loader.vue';
import RadioButton from "primevue/radiobutton";
import { vfmPlugin } from "vue-final-modal";
import { vue3Debounce } from 'vue-debounce';

import { createPinia } from 'pinia';
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate';
import {useVuelidate} from "@vuelidate/core";
import { createApp } from 'vue';

const pinia = createPinia();
pinia.use(piniaPluginPersistedstate)
const app = createApp({});

app.use(useVuelidate);
app.use(pinia);
app.use(vfmPlugin);
app.use(PrimeVue);
app.use(ToastService);
app.use(setupCalendar, {});
app.component('VCalendar', Calendar);
app.component('Datepicker', Datepicker);
app.component('Dropdown', Dropdown);
app.component('MultiSelect', MultiSelect);
app.component('SelectButton', SelectButton);
app.component('FileUpload', FileUpload);
app.component('RadioButton', RadioButton);
app.component('approvers-main', ApproversMain);
app.component('department-main', DepartmentMain);
app.component('dashboard-main', DashboardMain);
app.component('calendar-index', CalendarIndex);
app.component('profile-main', ProfileMain);
app.component('leaverequest-main', LeaveRequestMain);
app.component('leaverequest-history', LeaveRequestHistory);
app.component('leaverequest-approver', LeaveRequestApprover);
app.component('leave-summary', LeaveSummary);
app.component('employee-main', EmployeeMain);
app.component('administration-main', AdministrationMain);
app.component('update-leave-balance', UpdateLeaveBalance);
app.component('deduct-leave', DeductLeave);
app.component('redemption-index', CreateLeaveRedemption);
app.component('redemption-history', RedemptionHistory);
app.component('redemption-approve', RedemptionApprove);
app.component('redemption-summary', RedemptionSummary);
app.component('eform-index', EformIndex);
app.component('eform-apply', EformApply);
app.component('eform-approve', EformApprove);
app.component('eform-summary', EformSummary);
app.component('travel-form', TravelForm);
app.component('travel-show', TAPrint);
app.component('expense-show', TEShow);
app.component('expense-form', TEForm);
app.component('user-guide', UserGuide);

// TODO: PLEASE DO NOT USE SYSTEM-MSG, USE MSG-MODAL INSTEAD
app.component('system-msg', SystemMsg);
app.component('msg-modal', MsgModal);
app.component('error-modal', ErrorModal);
app.component('loader', Loader);
app.directive('tooltip', Tooltip);
app.directive('debounce', vue3Debounce({ lock: true }))
    .mount('#app');

import { sideBarJs } from "./custom/dashboard.js";
window.sideBarJs = sideBarJs();
