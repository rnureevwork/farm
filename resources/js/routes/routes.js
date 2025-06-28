import {useAuthStore} from "@/store/auth";

const AuthenticatedLayout = () => import('../layouts/Authenticated.vue')
const GuestLayout = ()  => import('../layouts/Guest.vue');

const PostsIndex  = ()  => import('../views/admin/posts/Index.vue');
const PostsCreate  = ()  => import('../views/admin/posts/Create.vue');
const PostsEdit  = ()  => import('../views/admin/posts/Edit.vue');

function requireLogin(to, from, next) {
    const auth = useAuthStore()
    if (auth.authenticated) {
        next()
    } else {
        next('/login')
    }
}

function guest(to, from, next) {
    const auth = useAuthStore()
    if (auth.authenticated) {
        next('/admin')
    } else {
        next()
    }
}

export default [
    {
        path: '/',
        // redirect: { name: 'login' },
        component: GuestLayout,
        children: [
            {
                path: '/',
                name: 'home',
                component: () => import('../views/home/index.vue'),
            },
            {
                path: 'posts',
                name: 'public-posts.index',
                component: () => import('../views/posts/index.vue'),
            },
            {
                path: 'posts/:id',
                name: 'public-posts.details',
                component: () => import('../views/posts/details.vue'),
            },
            {
                path: 'category/:id',
                name: 'category-posts.index',
                component: () => import('../views/category/posts.vue'),
            },
            {
                path: 'login',
                name: 'auth.login',
                component: () => import('../views/login/Login.vue'),
                beforeEnter: guest,
            },
            {
                path: 'register',
                name: 'auth.register',
                component: () => import('../views/register/index.vue'),
                beforeEnter: guest,
            },
            {
                path: 'forgot-password',
                name: 'auth.forgot-password',
                component: () => import('../views/auth/passwords/Email.vue'),
                beforeEnter: guest,
            },
            {
                path: 'reset-password/:token',
                name: 'auth.reset-password',
                component: () => import('../views/auth/passwords/Reset.vue'),
                beforeEnter: guest,
            },
        ]
    },
    {
        path: '/admin',
        component: AuthenticatedLayout,
        // redirect: {
        //     name: 'admin.index'
        // },
        beforeEnter: requireLogin,
        children: [
            {
                name: 'admin.index',
                path: '',
                component: () => import('../views/admin/index.vue'),
                meta: { breadCrumb: 'Admin' }
            },
            {
                name: 'profile.index',
                path: 'profile',
                component: () => import('../views/admin/profile/index.vue'),
                meta: { breadCrumb: 'Profile' }
            },
            {
                name: 'posts.index',
                path: 'posts',
                component: PostsIndex,
                meta: { breadCrumb: 'Posts' }
            },
            {
                name: 'posts.create',
                path: 'posts/create',
                component: PostsCreate,
                meta: { breadCrumb: 'Add new post' }
            },
            {
                name: 'posts.edit',
                path: 'posts/edit/:id',
                component: PostsEdit,
                meta: { breadCrumb: 'Edit post' }
            },
            {
                name: 'categories.index',
                path: 'categories',
                component: () => import('../views/admin/categories/Index.vue'),
                meta: { breadCrumb: 'Categories' }
            },
            {
                name: 'categories.create',
                path: 'categories/create',
                component: () => import('../views/admin/categories/Create.vue'),
                meta: { breadCrumb: 'Add new category' }
            },
            {
                name: 'categories.edit',
                path: 'categories/edit/:id',
                component: () => import('../views/admin/categories/Edit.vue'),
                meta: { breadCrumb: 'Edit Category' }
            },
            {
                name: 'permissions.index',
                path: 'permissions',
                component: () => import('../views/admin/permissions/Index.vue'),
                meta: { breadCrumb: 'Permissions' }
            },
            {
                name: 'permissions.create',
                path: 'permissions/create',
                component: () => import('../views/admin/permissions/Create.vue'),
                meta: { breadCrumb: 'Create Permission' }
            },
            {
                name: 'permissions.edit',
                path: 'permissions/edit/:id',
                component: () => import('../views/admin/permissions/Edit.vue'),
                meta: { breadCrumb: 'Permission Edit' }
            },
            {
                name: 'roles.index',
                path: 'roles',
                component: () => import('../views/admin/roles/Index.vue'),
                meta: { breadCrumb: 'Roles' }
            },
            {
                name: 'roles.create',
                path: 'roles/create',
                component: () => import('../views/admin/roles/Create.vue'),
                meta: { breadCrumb: 'Create Role' }
            },
            {
                name: 'roles.edit',
                path: 'roles/edit/:id',
                component: () => import('../views/admin/roles/Edit.vue'),
                meta: { breadCrumb: 'Role Edit' }
            },
            {
                name: 'users.index',
                path: 'users',
                component: () => import('../views/admin/users/Index.vue'),
                meta: { breadCrumb: 'Users' }
            },
            {
                name: 'users.create',
                path: 'users/create',
                component: () => import('../views/admin/users/Create.vue'),
                meta: { breadCrumb: 'Add New' }
            },
            {
                name: 'users.edit',
                path: 'users/edit/:id',
                component: () => import('../views/admin/users/Edit.vue'),
                meta: { breadCrumb: 'User Edit' }
            },
            {
                name: 'browser_sessions.index',
                path: 'browser-sessions',
                component: () => import('../views/admin/browser-sessions/Index.vue'),
                meta: { breadCrumb: 'Browser Sessions' }
            },
            {
                name: 'activity_log.index',
                path: 'activity-log-logs',
                component: () => import('../views/admin/activity-log/Index.vue'),
                meta: { breadCrumb: 'Activity Logs' }
            },
            {
                name: 'stations.index',
                path: 'stations',
                component: () => import('../views/admin/stations/Index.vue'),
                meta: { breadCrumb: 'Метеостанции' }
            },
            {
                name: 'stations.create',
                path: 'stations/create',
                component: () => import('../views/admin/stations/Create.vue'),
                meta: { breadCrumb: 'Добавить станцию' }
            },
            {
                name: 'stations.edit',
                path: 'stations/edit/:id',
                component: () => import('../views/admin/stations/Edit.vue'),
                meta: { breadCrumb: 'Редактировать станцию' }
            },
            {
                name: 'stations.data',
                path: 'stations/:id/data',
                component: () => import('../views/admin/stations/Data.vue'),
                meta: { breadCrumb: 'Данные станции' }
            },
            {
                name: 'stations.show',
                path: 'stations/:id',
                component: () => import('../views/admin/stations/Show.vue'),
                meta: { breadCrumb: 'Детали станции' }
            },
            {
                name: 'fields.index',
                path: 'fields',
                component: () => import('../views/admin/fields/Index.vue'),
                meta: { breadCrumb: 'Поля' }
            },
            {
                name: 'fields.create',
                path: 'fields/create',
                component: () => import('../views/admin/fields/Create.vue'),
                meta: { breadCrumb: 'Добавить поле' }
            },
            {
                name: 'fields.edit',
                path: 'fields/edit/:id',
                component: () => import('../views/admin/fields/Edit.vue'),
                meta: { breadCrumb: 'Редактировать поле' }
            },
            {
                name: 'fields.show',
                path: 'fields/:id',
                component: () => import('../views/admin/fields/Show.vue'),
                meta: { breadCrumb: 'Детали поля' }
            },
            {
                name: 'alerts.index',
                path: 'alerts',
                component: () => import('../views/admin/alerts/Index.vue'),
                meta: { breadCrumb: 'Уведомления' }
            },
            {
                name: 'alerts.show',
                path: 'alerts/:id',
                component: () => import('../views/admin/alerts/Show.vue'),
                meta: { breadCrumb: 'Детали уведомления' }
            },
            {
                name: 'dashboard',
                path: 'dashboard',
                component: () => import('../views/admin/dashboard/Index.vue'),
                meta: { breadCrumb: 'Дашборд' }
            },
            {
                name: 'farms.index',
                path: 'farms',
                component: () => import('../views/admin/farms/Index.vue'),
                meta: { breadCrumb: 'Фермы' }
            },
            {
                name: 'farms.create',
                path: 'farms/create',
                component: () => import('../views/admin/farms/Create.vue'),
                meta: { breadCrumb: 'Добавить ферму' }
            },
            {
                name: 'farms.edit',
                path: 'farms/edit/:id',
                component: () => import('../views/admin/farms/Edit.vue'),
                meta: { breadCrumb: 'Редактировать ферму' }
            },
            {
                name: 'farms.show',
                path: 'farms/:id',
                component: () => import('../views/admin/farms/Show.vue'),
                meta: { breadCrumb: 'Детали фермы' }
            },
            {
                name: 'crops.index',
                path: 'crops',
                component: () => import('../views/admin/crops/Index.vue'),
                meta: { breadCrumb: 'Культуры' }
            },
            {
                name: 'crops.create',
                path: 'crops/create',
                component: () => import('../views/admin/crops/Create.vue'),
                meta: { breadCrumb: 'Добавить культуру' }
            },
            {
                name: 'crops.edit',
                path: 'crops/edit/:id',
                component: () => import('../views/admin/crops/Edit.vue'),
                meta: { breadCrumb: 'Редактировать культуру' }
            },
        ]
    },
    {
        path: "/:pathMatch(.*)*",
        name: 'NotFound',
        component: () => import("../views/errors/404.vue"),
    },
];
