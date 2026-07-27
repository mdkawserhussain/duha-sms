import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: '/login',
        name: 'login',
        component: () => import('./pages/auth/Login.vue'),
        meta: { guest: true },
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('./pages/auth/Register.vue'),
        meta: { guest: true },
    },
    {
        path: '/',
        component: () => import('./layouts/AppLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                name: 'dashboard',
                component: () => import('./pages/Dashboard.vue'),
            },
            // ── Admin ──
            {
                path: 'admin',
                name: 'admin',
                component: () => import('./pages/admin/Index.vue'),
                meta: { role: 'admin' },
                children: [
                    { path: 'guardians', name: 'admin.guardians', component: () => import('./pages/admin/Guardians.vue') },
                    { path: 'students', name: 'admin.students', component: () => import('./pages/admin/Students.vue') },
                    { path: 'promotions', name: 'admin.promotions', component: () => import('./pages/admin/Promotions.vue') },
                    { path: 'teachers', name: 'admin.teachers', component: () => import('./pages/admin/Teachers.vue') },
                    { path: 'classes', name: 'admin.classes', component: () => import('./pages/admin/Classes.vue') },
                    { path: 'subjects', name: 'admin.subjects', component: () => import('./pages/admin/Subjects.vue') },
                    { path: 'rooms', name: 'admin.rooms', component: () => import('./pages/admin/Rooms.vue') },
                    { path: 'academic-years', name: 'admin.academicYears', component: () => import('./pages/admin/AcademicYears.vue') },
                    { path: 'terms', name: 'admin.terms', component: () => import('./pages/admin/Terms.vue') },
                    { path: 'attendance', name: 'admin.attendance', component: () => import('./pages/admin/Attendance.vue') },
                    { path: 'evaluations', name: 'admin.evaluations', component: () => import('./pages/admin/Evaluations.vue') },
                    { path: 'report-cards', name: 'admin.reportCards', component: () => import('./pages/admin/ReportCards.vue') },
                    { path: 'fees', name: 'admin.fees', component: () => import('./pages/admin/Fees.vue') },
                    { path: 'announcements', name: 'admin.announcements', component: () => import('./pages/admin/Announcements.vue') },
                    { path: 'events', name: 'admin.events', component: () => import('./pages/admin/Events.vue') },
                    { path: 'routines', name: 'admin.routines', component: () => import('./pages/admin/Routines.vue') },
                    { path: 'exam-routines', name: 'admin.examRoutines', component: () => import('./pages/admin/ExamRoutines.vue') },
                    { path: 'messages', name: 'admin.messages', component: () => import('./pages/admin/Messages.vue') },
                    { path: 'applications', name: 'admin.applications', component: () => import('./pages/admin/Applications.vue') },
                    { path: 'profile-change-requests', name: 'admin.profileChangeRequests', component: () => import('./pages/admin/ProfileChangeRequests.vue') },
                    { path: 'activity-log', name: 'admin.activityLog', component: () => import('./pages/admin/ActivityLog.vue') },
                    { path: 'settings', name: 'admin.settings', component: () => import('./pages/admin/SystemSettings.vue') },
                ],
            },
            // ── Teacher ──
            {
                path: 'teacher',
                name: 'teacher',
                component: () => import('./pages/teacher/Index.vue'),
                meta: { role: 'teacher' },
                children: [
                    { path: '', name: 'teacher.dashboard', component: () => import('./pages/teacher/Dashboard.vue') },
                    { path: 'classes', name: 'teacher.classes', component: () => import('./pages/teacher/Classes.vue') },
                    { path: 'attendance', name: 'teacher.attendance', component: () => import('./pages/teacher/Attendance.vue') },
                    { path: 'diary', name: 'teacher.diary', component: () => import('./pages/teacher/Diary.vue') },
                    { path: 'evaluations', name: 'teacher.evaluations', component: () => import('./pages/teacher/Evaluations.vue') },
                    { path: 'routine', name: 'teacher.routine', component: () => import('./pages/teacher/Routine.vue') },
                    { path: 'exam-routines', name: 'teacher.examRoutines', component: () => import('./pages/teacher/ExamRoutines.vue') },
                    { path: 'messages', name: 'teacher.messages', component: () => import('./pages/teacher/Messages.vue') },
                    { path: 'leave-notifications', name: 'teacher.leaveNotifications', component: () => import('./pages/teacher/LeaveNotifications.vue') },
                ],
            },
            // ── Guardian ──
            {
                path: 'guardian',
                name: 'guardian',
                component: () => import('./pages/guardian/Index.vue'),
                meta: { role: 'guardian' },
                children: [
                    { path: '', name: 'guardian.dashboard', component: () => import('./pages/guardian/Dashboard.vue') },
                    { path: 'children', name: 'guardian.children', component: () => import('./pages/guardian/Children.vue') },
                    { path: 'attendance/:studentId?', name: 'guardian.attendance', component: () => import('./pages/guardian/Attendance.vue') },
                    { path: 'diary/:studentId?', name: 'guardian.diary', component: () => import('./pages/guardian/Diary.vue') },
                    { path: 'evaluations/:studentId?', name: 'guardian.evaluations', component: () => import('./pages/guardian/Evaluations.vue') },
                    { path: 'report-cards/:studentId?', name: 'guardian.reportCards', component: () => import('./pages/guardian/ReportCards.vue') },
                    { path: 'fees/:studentId?', name: 'guardian.fees', component: () => import('./pages/guardian/Fees.vue') },
                    { path: 'profile-change-request', name: 'guardian.profileChangeRequest', component: () => import('./pages/guardian/ProfileChangeRequest.vue') },
                    { path: 'messages', name: 'guardian.messages', component: () => import('./pages/guardian/Messages.vue') },
                    { path: 'leave-notifications', name: 'guardian.leaveNotifications', component: () => import('./pages/guardian/LeaveNotifications.vue') },
                ],
            },
        ],
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const isAuthenticated = localStorage.getItem('auth_token');
    const userRole = localStorage.getItem('user_role');

    if (to.meta.requiresAuth && !isAuthenticated) {
        next({ name: 'login' });
    } else if (to.meta.guest && isAuthenticated) {
        next({ name: 'dashboard' });
    } else if (to.meta.role && to.meta.role !== userRole) {
        next({ name: 'dashboard' });
    } else {
        next();
    }
});

export default router;
