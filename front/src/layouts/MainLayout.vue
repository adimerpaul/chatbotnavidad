<template>
  <q-layout view="lHh Lpr lFf">
    <q-header class="bg-white text-black" bordered>
      <q-toolbar>
        <q-btn
          flat
          color="primary"
          :icon="leftDrawerOpen ? 'keyboard_double_arrow_left' : 'keyboard_double_arrow_right'"
          aria-label="Menu"
          @click="toggleLeftDrawer"
          unelevated
          dense
        />
        <q-toolbar-title>
          <span class="text-caption text-bold">
            {{ $version }}
          </span>
        </q-toolbar-title>

        <div>
          <q-btn-dropdown flat unelevated no-caps dropdownIcon="expand_more">
            <template v-slot:label>
              <q-avatar rounded>
                <q-img :src="`${$url}/../images/${$store.user.avatar}`" width="40px" height="40px" v-if="$store.user.avatar" />
              </q-avatar>
              <div class="text-center" style="line-height: 1">
                <div style="width: 100px; white-space: normal; overflow-wrap: break-word;">
                  {{ $store.user.username }}
                  <br>
                  <q-chip dense size="10px" :color="$filters.color($store.user.role)" text-color="white">
                    {{ $store.user.role }}
                  </q-chip>
                </div>
              </div>
            </template>
            <q-item clickable v-ripple @click="logout" v-close-popup>
              <q-item-section avatar>
                <q-icon name="logout" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Salir</q-item-label>
              </q-item-section>
            </q-item>
          </q-btn-dropdown>
        </div>
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      bordered
      show-if-above
      :width="200"
      :breakpoint="500"
      class="bg-primary text-white"
    >
      <q-list>
        <q-item-label header class="text-center">
<!--          <template v-if="$store.user.agencia" >-->
            <q-img src="/logo-light.png" width="100px"  />
<!--          </template>-->
        </q-item-label>

        <template v-for="link in filteredLinks" :key="link.title">
          <q-item clickable :to="link.link" exact
                  class="text-black"
                  active-class="menu"
                  dense
                  v-close-popup
          >
            <q-item-section avatar>
              <q-icon :name="$route.path === link.link ? 'o_' + link.icon : link.icon"
                      :class="$route.path === link.link ? 'text-black' : 'text-grey'" />
            </q-item-section>
            <q-item-section>
              <q-item-label :class="$route.path === link.link ? 'text-black text-bold' : 'text-grey'">
                {{ link.title }}
              </q-item-label>
            </q-item-section>
          </q-item>
        </template>

        <q-item clickable class="text-grey" @click="logout" v-close-popup>
          <q-item-section avatar>
            <q-icon name="logout" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Salir</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </q-drawer>

    <q-page-container class="bg-grey-3">
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { computed, getCurrentInstance, ref } from 'vue'

const { proxy } = getCurrentInstance()

const linksList = [
  {title: 'Principal', icon: 'home', link: '/', can: 'Todos'},
  {title: 'Usuarios', icon: 'people', link: '/usuarios', can: 'Administrador'},
  {title: 'Cursos', icon: 'school', link: '/cursos', can: [ 'Administrador']},
  {title: 'Estudiantes', icon: 'groups', link: '/estudiantes', can: [ 'Administrador']},
  {title: 'Docentes', icon: 'person', link: '/docentes', can: [ 'Administrador']},
  {title: 'Asignaciones', icon: 'assignment', link: '/asignaciones', can: [ 'Administrador']},
  // {title: 'Asistencia', icon: 'check_circle', link: '/asistencia', can: ['Docente', 'Administrador']},
  {title: 'Mis Cursos', icon: 'school', link: '/mis-cursos', can: ['Docente', 'Administrador']},
]

const leftDrawerOpen = ref(false)

function toggleLeftDrawer() {
  leftDrawerOpen.value = !leftDrawerOpen.value
}

function logout() {
  proxy.$alert.dialog('¿Desea salir del sistema?')
    .onOk(() => {
      // proxy.$store.isLogged = false
      // proxy.$store.user = {}
      // localStorage.removeItem('tokenAsistencia')
      // proxy.$router.push('/login')
      proxy.$axios.post('/logout')
        .then(() => {
          proxy.$store.isLogged = false
          proxy.$store.user = {}
          localStorage.removeItem('tokenAsistencia')
          proxy.$router.push('/login')
        })
        .catch(error => {
          console.error('Error al cerrar sesión:', error)
          proxy.$alert.error('Error al cerrar sesión. Intente nuevamente.')
        })
    })
}

const filteredLinks = computed(() => {
  // const userPermissions = proxy.$store.user.userPermisos || []
  //
  // const permissionIds = userPermissions.map(p => p.permisoId)
  //
  // return linksList.filter(link => {
  //   if (link.can === 'Todos') {
  //     return true
  //   }
  //   return permissionIds.includes(link.can)
  // })
  return linksList.filter(link => {
    if (link.can === 'Todos') {
      return true
    }
    if (Array.isArray(link.can)) {
      return link.can.includes(proxy.$store.user.role)
    }
    return proxy.$store.user.role === link.can
  })
})
// computed
// const getColorRole = computed(() => {
//   const role = proxy.$store.user.role
//   if (role === 'Administrador') {
//     return 'red'
//   } else if (role === 'Docente') {
//     return 'green'
//   } else if (role === 'Estudiante') {
//     return 'blue'
//   }
//   return ''
// })
</script>

<style>
.menu {
  background-color: #fff;
  color: #000 !important;
  border-radius: 10px;
  margin: 5px;
  padding: 5px
}
</style>
