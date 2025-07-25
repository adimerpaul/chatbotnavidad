const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      {path: '', component: () => import('pages/IndexPage.vue'), meta: {requiresAuth: true}},
      {path: '/usuarios', component: () => import('pages/usuarios/Usuarios.vue'), meta: {requiresAuth: true}},
      {path: '/doctor', component: () => import('pages/doctor/Doctor.vue'), meta: {requiresAuth: true}},
      {path: '/preguntas', component: () => import('pages/preguntas/Preguntas.vue'), meta: {requiresAuth: true}},
      {path: '/atencionCliente', component: () => import('pages/atencionCliente/AtencionCliente.vue'), meta: {requiresAuth: true}},
      {path: '/calendar', component: () => import('pages/calendar/Calendar.vue'), meta: {requiresAuth: true}},
    ]
  },
  {
    path: '/login',
    component: () => import('layouts/Login.vue'),
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue')
  }
]

export default routes
