<template>
  <q-layout class="bg-green-blue" view="lHh Lpr lff">
    <q-page-container>
      <q-page class="flex flex-center">
        <div class="row">
          <div class="col-12 col-md-1"></div>
          <div :class="`col-12 col-md-10 ${$q.screen.gt.sm ? 'q-pa-lg' : ''}`">
            <q-card flat bordered class="q-ma-xs q-pa-none">
              <q-card-section class="q-pa-none">
                <div class="row">
                  <div class="col-12 col-md-6">
                    <div class="bg-fondo">
                      <q-carousel
                        v-model="slide"
                        transition-prev="scale"
                        transition-next="scale"
                        swipeable
                        animated
                        infinite
                        autoplay
                        control-color="white"
                        navigation
                        padding
                        arrows
                        :height="$q.screen.gt.sm ? '600px' : '230px'"
                        class="text-white shadow-1 rounded-borders bg-fondo"
                      >
                        <q-carousel-slide
                          v-for="(slide, index) in slides"
                          :key="index"
                          :name="slide.name"
                          class="column no-wrap flex-center"
                        >
                          <q-icon :name="slide.icon" size="50px"/>
                          <div class="text-center">
                            <div class="q-mb-xs text-bold">{{ slide.title }}</div>
                            <p class="text-caption">{{ slide.description }}</p>
                          </div>
                        </q-carousel-slide>
                        <template v-slot:control>
                          <q-carousel-control
                            position="top-left"
                            :offset="[5, 10]"
                            class="text-white rounded-borders"
                          >
                            <img src="logo-light.png" width="140"/>
                          </q-carousel-control>
                        </template>
                      </q-carousel>
                    </div>
                  </div>
                  <div :class="'col-12 col-md-6 q-pa-md bg-white flex flex-center'">
                    <q-form @submit.prevent="loginSubmit">
                    <div class="row q-ma-md">
                      <div class="col-12">
                        <div class="" style="color: #5F5189">Bienvenido a Vela Educa</div>
                        <div class="text-grey q-py-xs">Inicia sesión para continuar</div>
                      </div>
                      <div class="col-12">
                        <label class="text-subtitle">Usuario</label>
                        <q-input v-model="login.username" outlined type="text" placeholder="Usuario"
                                 :rules="[
                            (val) => !!val || 'El usuario es requerido',
                            (val) => val.length >= 3 || 'El usuario debe tener al menos 3 caracteres',
                            ]"
                                 dense/>
                      </div>
                      <div class="col-12">
                        <label class="text-subtitle">Contraseña</label>
                        <q-input
                          v-model="login.password"
                          outlined
                          :type="showPassword ? 'text' : 'password'"
                          placeholder="Contraseña"
                          dense
                          :rules="[
                            (val) => !!val || 'La contraseña es requerida',
                          ]"
                        >
                          <template v-slot:append>
                            <q-icon
                              :name="showPassword ? 'visibility' : 'visibility_off'"
                              @click.stop="showPassword = !showPassword"
                              class="cursor-pointer"
                            />
                          </template>
                        </q-input>
<!--                        <pre>{{login}}</pre>-->
                      </div>
                      <div class="col-12">
                        <q-checkbox
                          v-model="remember"
                          label="Recordar mi usuario"
                          color="primary"
                        />
                      </div>
                      <div class="col-12">
                        <q-btn
                          :loading="loading"
                          :disable="loading"
                          label="Iniciar sesión"
                          color="positive"
                          no-caps
                          type="submit"
                          class="full-width"/>
                      </div>
                      <div class="col-12">
                        <div class="text-center">
                          <q-btn
                            flat
                            label="Olvidé mi contraseña"
                            color="primary"
                            no-caps
                            :loading="loading"
                          />
                        </div>
                      </div>
                    </div>
                    </q-form>
                  </div>
                </div>
              </q-card-section>
            </q-card>
            <div>
<!--              <q-separator/>-->
              <div class="text-center">
                <div class="text-caption text-white">Vela Educa © {{ new Date().getFullYear() }}. Todos los derechos reservados.</div>
                <div class="text-caption text-white">Desarrollado por Vela Educa</div>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-1"></div>
        </div>
      </q-page>
    </q-page-container>
<!--    <q-footer class="bg-white">-->
<!--      <div class="text-center">-->
<!--        <p class="text-caption text-grey">Vela Educa © {{ new Date().getFullYear() }}. Todos los derechos reservados.</p>-->
<!--        <p class="text-caption text-grey">Desarrollado por Vela Educa</p>-->
<!--      </div>-->
<!--    </q-footer>-->
  </q-layout>
</template>
<script>
export default {
  name: 'Login',
  data() {
    return {
      slide: 'automation',
      slides: [
        {
          name: 'automation',
          icon: 'smart_toy',
          title: 'Automatización de Procesos',
          description: 'Un sistema de información reduce el trabajo manual y mejora la eficiencia operativa.'
        },
        {
          name: 'access',
          icon: 'cloud_queue',
          title: 'Acceso a la Información',
          description: 'Permite obtener datos en tiempo real desde cualquier dispositivo con conexión.'
        },
        {
          name: 'security',
          icon: 'security',
          title: 'Seguridad de Datos',
          description: 'Protege la información mediante autenticación, permisos y respaldos automáticos.'
        },
        {
          name: 'decision',
          icon: 'insights',
          title: 'Toma de Decisiones',
          description: 'Ayuda a tomar decisiones basadas en datos concretos, no suposiciones.'
        },
        {
          name: 'integration',
          icon: 'hub',
          title: 'Integración de Áreas',
          description: 'Conecta diferentes departamentos para un flujo de trabajo coordinado y eficiente.'
        }
      ],
      login: {
        username: '',
        password: '',
      },
      showPassword: false,
      loading: false,
      remember: false,
    };
  },
  methods: {
    loginSubmit() {
      this.loading = true;
      this.$axios.post('login', {
        username: this.login.username,
        password: this.login.password,
      }).then(response => {
        // console.log(response.data);
        this.$store.isLogged = true;
        this.$store.user = response.data.user;
        this.$axios.defaults.headers.common['Authorization'] = 'Bearer ' + response.data.token;
        localStorage.setItem('tokenAsistencia', response.data.token);
        localStorage.setItem('user', JSON.stringify(response.data.user));
        this.$alert.success('Bienvenido ' + response.data.user.name);
        this.$router.push('/');
      }).catch(error => {
        this.loading = false;
        this.$alert.error(error.response.data.message || 'Error de conexión');
      }).finally(() => {
        this.loading = false;
      });
    },
  },
};
</script>
<style>
.bg-green-blue {
  background-image: linear-gradient(to bottom right, rgba(12, 202, 176, 0.7), rgba(64, 81, 137, 0.7)),
  url('./../cover-pattern.png');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  /*height: 100vh;*/
}

.bg-fondo {
  background-image: linear-gradient(to bottom right, rgba(64, 81, 137, 0.7)),
  url('./../auth-one-bg.jpg');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
}

</style>
