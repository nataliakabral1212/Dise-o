<template>
  <div class="root-bg">
    <!-- Fondo flores -->
    <div class="bg-flowers">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        style="position: absolute; inset: 0; width: 100%; height: 100%"
        viewBox="0 0 1440 900"
        preserveAspectRatio="xMidYMid slice"
      >
        <ellipse
          cx="100"
          cy="80"
          rx="22"
          ry="54"
          fill="#D4A8B0"
          opacity="0.18"
          transform="rotate(-20,100,80)"
        />
        <ellipse
          cx="1380"
          cy="820"
          rx="28"
          ry="68"
          fill="#C4A882"
          opacity="0.15"
          transform="rotate(15,1380,820)"
        />
        <ellipse
          cx="1350"
          cy="50"
          rx="20"
          ry="50"
          fill="#D47878"
          opacity="0.12"
          transform="rotate(40,1350,50)"
        />
        <ellipse
          cx="60"
          cy="860"
          rx="25"
          ry="60"
          fill="#D4A8B0"
          opacity="0.14"
          transform="rotate(-30,60,860)"
        />
      </svg>
    </div>

    <!-- CARD -->
    <div class="card">
      <!-- ══════════════════════════════════
           ZONA IZQUIERDA (55%) — formulario cuando panel está a la izq
           ZONA DERECHA  (55%) — formulario cuando panel está a la der
           Usamos dos zonas fijas; mostramos la correcta según el modo
      ══════════════════════════════════ -->

      <!-- Zona izquierda del form (visible en LOGIN, panel a la izquierda tapa parte) -->
      <!-- Zona derecha del form (visible en REGISTER, panel se fue a la derecha) -->

      <!-- Contenedor que se "empuja" según dónde está el panel -->
      <div class="form-zone" :class="{ 'form-zone--right': isRegister }">
        <transition name="form-anim" mode="out-in">
          <!-- LOGIN -->
          <div v-if="!isRegister" key="login" class="form-inner">
            <div class="logo-header">
              <div class="logo-wrap">
                <img
                  :src="'./src/assets/logoSrc.jpg'"
                  alt="Nikté Logo"
                  style="width: 82px; height: 82px; object-fit: contain"
                />
              </div>
              <h1 class="logo-title">Nikté</h1>
              <p class="logo-sub">Iniciar Sesión</p>
            </div>

            <div class="field-wrap">
              <label class="field-label">Correo electrónico</label>
              <div class="field-inner">
                <svg
                  width="14"
                  height="14"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="#C4A882"
                  stroke-width="1.8"
                  style="flex-shrink: 0"
                >
                  <path
                    d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"
                  />
                  <polyline points="22,6 12,13 2,6" />
                </svg>
                <input
                  v-model="loginForm.email"
                  type="email"
                  placeholder="tu@correo.com"
                  class="field-input"
                />
              </div>
            </div>

            <div class="field-wrap">
              <label class="field-label">Contraseña</label>
              <div class="field-inner">
                <svg
                  width="14"
                  height="14"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="#C4A882"
                  stroke-width="1.8"
                  style="flex-shrink: 0"
                >
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                  <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
                <input
                  v-model="loginForm.password"
                  :type="showPwd ? 'text' : 'password'"
                  placeholder="••••••••"
                  class="field-input"
                />
                <button
                  type="button"
                  @click="showPwd = !showPwd"
                  class="eye-btn"
                >
                  <svg
                    v-if="!showPwd"
                    width="14"
                    height="14"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                  >
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                    <circle cx="12" cy="12" r="3" />
                  </svg>
                  <svg
                    v-else
                    width="14"
                    height="14"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                  >
                    <path
                      d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"
                    />
                    <line x1="1" y1="1" x2="23" y2="23" />
                  </svg>
                </button>
              </div>
            </div>

            <div class="forgot-row">
              <a href="#" class="forgot-link">¿Olvidaste tu contraseña?</a>
            </div>

            <div v-if="loginErr" class="error-msg">{{ loginErr }}</div>

            <button
              @click="handleLogin"
              :disabled="loading"
              class="submit-btn"
              :style="{ opacity: loading ? 0.7 : 1 }"
            >
              <svg
                v-if="loading"
                width="14"
                height="14"
                viewBox="0 0 24 24"
                fill="none"
                stroke="white"
                stroke-width="2.5"
                style="animation: spin 1s linear infinite"
              >
                <circle cx="12" cy="12" r="10" stroke-opacity="0.3" />
                <path d="M12 2a10 10 0 0 1 10 10" />
              </svg>
              <span>{{ loading ? "Ingresando..." : "Iniciar sesión" }}</span>
              <svg
                v-if="!loading"
                width="14"
                height="14"
                viewBox="0 0 24 24"
                fill="none"
                stroke="white"
                stroke-width="2.5"
              >
                <path d="M5 12h14M12 5l7 7-7 7" />
              </svg>
            </button>

            <p class="switch-row">
              ¿No tienes cuenta?
              <a href="#" @click.prevent="switchMode" class="switch-link"
                >Regístrate</a
              >
            </p>
          </div>

          <!-- REGISTER -->
          <div v-else key="register" class="form-inner">
            <div class="logo-header">
              <div class="logo-wrap" style="width: 70px; height: 70px">
                <img
                  :src="'./src/assets/logoSrc.jpg'"
                  alt="Nikté Logo"
                  style="width: 62px; height: 62px; object-fit: contain"
                />
              </div>
              <h1 class="logo-title" style="font-size: 24px">Nikté</h1>
              <p class="logo-sub">Crear Cuenta</p>
            </div>

            <div class="field-wrap">
              <label class="field-label">Nombre completo</label>
              <div class="field-inner">
                <svg
                  width="14"
                  height="14"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="#C4A882"
                  stroke-width="1.8"
                  style="flex-shrink: 0"
                >
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                  <circle cx="12" cy="7" r="4" />
                </svg>
                <input
                  v-model="regForm.name"
                  type="text"
                  placeholder="Tu nombre"
                  class="field-input"
                />
              </div>
            </div>

            <div class="field-wrap">
              <label class="field-label">Correo electrónico</label>
              <div class="field-inner">
                <svg
                  width="14"
                  height="14"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="#C4A882"
                  stroke-width="1.8"
                  style="flex-shrink: 0"
                >
                  <path
                    d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"
                  />
                  <polyline points="22,6 12,13 2,6" />
                </svg>
                <input
                  v-model="regForm.email"
                  type="email"
                  placeholder="tu@correo.com"
                  class="field-input"
                />
              </div>
            </div>

            <div class="field-wrap">
              <label class="field-label">Contraseña</label>
              <div class="field-inner">
                <svg
                  width="14"
                  height="14"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="#C4A882"
                  stroke-width="1.8"
                  style="flex-shrink: 0"
                >
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                  <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
                <input
                  v-model="regForm.password"
                  :type="showRegPwd ? 'text' : 'password'"
                  placeholder="••••••••"
                  class="field-input"
                />
                <button
                  type="button"
                  @click="showRegPwd = !showRegPwd"
                  class="eye-btn"
                >
                  <svg
                    v-if="!showRegPwd"
                    width="14"
                    height="14"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                  >
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                    <circle cx="12" cy="12" r="3" />
                  </svg>
                  <svg
                    v-else
                    width="14"
                    height="14"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                  >
                    <path
                      d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"
                    />
                    <line x1="1" y1="1" x2="23" y2="23" />
                  </svg>
                </button>
              </div>
            </div>

            <div v-if="regErr" class="error-msg">{{ regErr }}</div>

            <button
              @click="handleRegister"
              :disabled="loading"
              class="submit-btn"
              :style="{ opacity: loading ? 0.7 : 1 }"
            >
              <svg
                v-if="loading"
                width="14"
                height="14"
                viewBox="0 0 24 24"
                fill="none"
                stroke="white"
                stroke-width="2.5"
                style="animation: spin 1s linear infinite"
              >
                <circle cx="12" cy="12" r="10" stroke-opacity="0.3" />
                <path d="M12 2a10 10 0 0 1 10 10" />
              </svg>
              <span>{{ loading ? "Creando cuenta..." : "Crear cuenta" }}</span>
              <svg
                v-if="!loading"
                width="14"
                height="14"
                viewBox="0 0 24 24"
                fill="none"
                stroke="white"
                stroke-width="2.5"
              >
                <path d="M5 12h14M12 5l7 7-7 7" />
              </svg>
            </button>

            <p class="switch-row">
              ¿Ya tienes cuenta?
              <a href="#" @click.prevent="switchMode" class="switch-link"
                >Inicia sesión</a
              >
            </p>
          </div>
        </transition>
      </div>

      <!-- ══════════════════════════════════
           PANEL DECORATIVO DESLIZANTE
      ══════════════════════════════════ -->
      <div class="deco-track" :class="{ 'to-right': isRegister }">
        <div class="deco-panel">
          <!-- Flores SVG -->
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 450 580"
            preserveAspectRatio="xMidYMid slice"
            class="deco-svg"
          >
            <ellipse
              cx="90"
              cy="440"
              rx="55"
              ry="135"
              fill="#D4A8B0"
              opacity="0.72"
              transform="rotate(0,90,580)"
            />
            <ellipse
              cx="90"
              cy="440"
              rx="55"
              ry="135"
              fill="#C4A882"
              opacity="0.58"
              transform="rotate(60,90,580)"
            />
            <ellipse
              cx="90"
              cy="440"
              rx="55"
              ry="135"
              fill="#D4A8B0"
              opacity="0.68"
              transform="rotate(120,90,580)"
            />
            <ellipse
              cx="90"
              cy="440"
              rx="55"
              ry="135"
              fill="#C4A882"
              opacity="0.55"
              transform="rotate(180,90,580)"
            />
            <ellipse
              cx="90"
              cy="440"
              rx="55"
              ry="135"
              fill="#D4A8B0"
              opacity="0.65"
              transform="rotate(240,90,580)"
            />
            <ellipse
              cx="90"
              cy="440"
              rx="55"
              ry="135"
              fill="#C4A882"
              opacity="0.58"
              transform="rotate(300,90,580)"
            />
            <circle cx="90" cy="580" r="38" fill="#C4A882" opacity="0.5" />
            <circle cx="90" cy="580" r="22" fill="#F5EDE0" opacity="0.8" />
            <ellipse
              cx="330"
              cy="60"
              rx="36"
              ry="88"
              fill="#D47878"
              opacity="0.55"
              transform="rotate(0,330,180)"
            />
            <ellipse
              cx="330"
              cy="60"
              rx="36"
              ry="88"
              fill="#D4A8B0"
              opacity="0.5"
              transform="rotate(72,330,180)"
            />
            <ellipse
              cx="330"
              cy="60"
              rx="36"
              ry="88"
              fill="#D47878"
              opacity="0.52"
              transform="rotate(144,330,180)"
            />
            <ellipse
              cx="330"
              cy="60"
              rx="36"
              ry="88"
              fill="#D4A8B0"
              opacity="0.48"
              transform="rotate(216,330,180)"
            />
            <ellipse
              cx="330"
              cy="60"
              rx="36"
              ry="88"
              fill="#C4A882"
              opacity="0.5"
              transform="rotate(288,330,180)"
            />
            <circle cx="330" cy="180" r="28" fill="#8B6B3D" opacity="0.45" />
            <circle cx="330" cy="180" r="16" fill="#C4A882" opacity="0.55" />
            <circle cx="330" cy="180" r="8" fill="#F5EDE0" opacity="0.8" />
            <ellipse
              cx="230"
              cy="290"
              rx="16"
              ry="38"
              fill="#D4A8B0"
              opacity="0.38"
              transform="rotate(0,230,330)"
            />
            <ellipse
              cx="230"
              cy="290"
              rx="16"
              ry="38"
              fill="#C4A882"
              opacity="0.32"
              transform="rotate(72,230,330)"
            />
            <ellipse
              cx="230"
              cy="290"
              rx="16"
              ry="38"
              fill="#D47878"
              opacity="0.35"
              transform="rotate(144,230,330)"
            />
            <ellipse
              cx="230"
              cy="290"
              rx="16"
              ry="38"
              fill="#D4A8B0"
              opacity="0.32"
              transform="rotate(216,230,330)"
            />
            <ellipse
              cx="230"
              cy="290"
              rx="16"
              ry="38"
              fill="#C4A882"
              opacity="0.35"
              transform="rotate(288,230,330)"
            />
            <circle cx="230" cy="330" r="14" fill="#C4A882" opacity="0.35" />
            <circle cx="230" cy="330" r="7" fill="#F5EDE0" opacity="0.6" />
          </svg>

          <!-- Texto del panel -->
          <transition name="panel-text" mode="out-in">
            <div v-if="!isRegister" key="txt-login" class="deco-text">
              <p class="deco-eyebrow">Salón de Belleza</p>
              <h2 class="deco-title">¡Bienvenida<br />de vuelta!</h2>
              <p class="deco-sub">
                Ingresa a tu cuenta para gestionar tus citas y servicios
                exclusivos.
              </p>
              <div class="floral-divider">
                <div class="div-line left"></div>
                <svg width="32" height="32" viewBox="0 0 32 32">
                  <ellipse
                    cx="16"
                    cy="9"
                    rx="3"
                    ry="7"
                    fill="#D4A8B0"
                    transform="rotate(0,16,16)"
                  />
                  <ellipse
                    cx="16"
                    cy="9"
                    rx="3"
                    ry="7"
                    fill="#C4A882"
                    transform="rotate(60,16,16)"
                  />
                  <ellipse
                    cx="16"
                    cy="9"
                    rx="3"
                    ry="7"
                    fill="#D47878"
                    transform="rotate(120,16,16)"
                  />
                  <ellipse
                    cx="16"
                    cy="9"
                    rx="3"
                    ry="7"
                    fill="#C4A882"
                    transform="rotate(180,16,16)"
                  />
                  <ellipse
                    cx="16"
                    cy="9"
                    rx="3"
                    ry="7"
                    fill="#D4A8B0"
                    transform="rotate(240,16,16)"
                  />
                  <ellipse
                    cx="16"
                    cy="9"
                    rx="3"
                    ry="7"
                    fill="#D47878"
                    transform="rotate(300,16,16)"
                  />
                  <circle cx="16" cy="16" r="4" fill="#8B6B3D" />
                  <circle cx="16" cy="16" r="2" fill="#F5EDE0" />
                </svg>
                <div class="div-line right"></div>
              </div>
            </div>
            <div v-else key="txt-register" class="deco-text">
              <p class="deco-eyebrow">Salón de Belleza</p>
              <h2 class="deco-title">¡Únete a<br />nosotros!</h2>
              <p class="deco-sub">
                Crea tu cuenta y descubre todos nuestros servicios de belleza
                exclusivos.
              </p>
              <div class="floral-divider">
                <div class="div-line left"></div>
                <svg width="32" height="32" viewBox="0 0 32 32">
                  <ellipse
                    cx="16"
                    cy="9"
                    rx="3"
                    ry="7"
                    fill="#D4A8B0"
                    transform="rotate(0,16,16)"
                  />
                  <ellipse
                    cx="16"
                    cy="9"
                    rx="3"
                    ry="7"
                    fill="#C4A882"
                    transform="rotate(60,16,16)"
                  />
                  <ellipse
                    cx="16"
                    cy="9"
                    rx="3"
                    ry="7"
                    fill="#D47878"
                    transform="rotate(120,16,16)"
                  />
                  <ellipse
                    cx="16"
                    cy="9"
                    rx="3"
                    ry="7"
                    fill="#C4A882"
                    transform="rotate(180,16,16)"
                  />
                  <ellipse
                    cx="16"
                    cy="9"
                    rx="3"
                    ry="7"
                    fill="#D4A8B0"
                    transform="rotate(240,16,16)"
                  />
                  <ellipse
                    cx="16"
                    cy="9"
                    rx="3"
                    ry="7"
                    fill="#D47878"
                    transform="rotate(300,16,16)"
                  />
                  <circle cx="16" cy="16" r="4" fill="#8B6B3D" />
                  <circle cx="16" cy="16" r="2" fill="#F5EDE0" />
                </svg>
                <div class="div-line right"></div>
              </div>
            </div>
          </transition>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from "vue";

const logoSrc = `data:image/png;base64,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`;

const isRegister = ref(false);
const showPwd = ref(false);
const showRegPwd = ref(false);
const loading = ref(false);
const loginErr = ref("");
const regErr = ref("");

const loginForm = reactive({ email: "", password: "" });
const regForm = reactive({ name: "", email: "", password: "" });

const switchMode = () => {
  isRegister.value = !isRegister.value;
  loginErr.value = "";
  regErr.value = "";
};

const handleLogin = async () => {
  loginErr.value = "";
  if (!loginForm.email || !loginForm.password) {
    loginErr.value = "Por favor completa todos los campos.";
    return;
  }
  loading.value = true;
  await new Promise((r) => setTimeout(r, 1500));
  loading.value = false;
  if (loginForm.email !== "admin@nikte.com")
    loginErr.value = "Correo o contraseña incorrectos.";
};

const handleRegister = async () => {
  regErr.value = "";
  if (!regForm.name || !regForm.email || !regForm.password) {
    regErr.value = "Por favor completa todos los campos.";
    return;
  }
  loading.value = true;
  await new Promise((r) => setTimeout(r, 1500));
  loading.value = false;
};
</script>

<style>
@import url("https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,600;0,700;1,400&family=Jost:wght@400;500;600&display=swap");
*,
*::before,
*::after {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}
html,
body,
#app {
  width: 100%;
  height: 100%;
  overflow: hidden;
}
input::placeholder {
  color: rgba(168, 150, 160, 0.5) !important;
}
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* ─── Root ─── */
.root-bg {
  position: fixed;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f0e8dc;
  font-family: "Jost", sans-serif;
}
.bg-flowers {
  position: absolute;
  inset: 0;
  overflow: hidden;
  pointer-events: none;
}

/* ─── Card ─── */
.card {
  position: relative;
  width: 900px;
  max-width: 96vw;
  height: 580px;
  max-height: 92vh;
  border-radius: 28px;
  overflow: hidden;
  box-shadow:
    0 32px 80px rgba(122, 78, 56, 0.22),
    0 8px 32px rgba(0, 0, 0, 0.08);
  background: #fff;
}

/* ══════════════════════════════════════════════════════
   FORM ZONE
   La zona donde vive el formulario.
   - En LOGIN:    ocupa el lado derecho (left: 45%, width: 55%)
   - En REGISTER: ocupa el lado izquierdo (left: 0, width: 55%)
   Se mueve CON LA MISMA DURACIÓN que el panel decorativo.
══════════════════════════════════════════════════════ */
.form-zone {
  position: absolute;
  top: 0;
  bottom: 0;
  /* Modo login: el panel deco está a la izquierda, el form está a la derecha */
  left: 45%;
  width: 55%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: left 0.68s cubic-bezier(0.86, 0, 0.07, 1);
  background: #fff;
  z-index: 1;
}

/* Modo register: el panel deco se fue a la derecha, el form ocupa la izquierda */
.form-zone--right {
  left: 0;
}

.form-inner {
  width: 100%;
  max-width: 300px;
  padding: 0 24px;
}

/* ══════════════════════════════════════════════════════
   PANEL DECORATIVO
   - En LOGIN:    left: 0  (ocupa el 45% izquierdo)
   - En REGISTER: left: 55% (ocupa el 45% derecho)
   Transición idéntica a la del form-zone para que se muevan juntos.
══════════════════════════════════════════════════════ */
.deco-track {
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  width: 45%;
  z-index: 10;
  transition: left 0.68s cubic-bezier(0.86, 0, 0.07, 1);
}
.deco-track.to-right {
  left: 55%;
}

.deco-panel {
  position: relative;
  width: 100%;
  height: 100%;
  background: #f5eee6;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow:
    4px 0 40px rgba(122, 78, 56, 0.16),
    -4px 0 40px rgba(122, 78, 56, 0.16);
}

.deco-svg {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
}

/* ─── Texto panel deco ─── */
.deco-text {
  position: relative;
  z-index: 2;
  text-align: center;
  padding: 0 32px;
}
.deco-eyebrow {
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.35em;
  text-transform: uppercase;
  color: #8f7350;
  margin: 0 0 12px;
}
.deco-title {
  font-family: "Cormorant Garamond", Georgia, serif;
  font-size: 38px;
  font-weight: 700;
  color: #7a4e38;
  margin: 0 0 14px;
  line-height: 1.15;
}
.deco-sub {
  font-size: 13px;
  color: #a896a0;
  line-height: 1.6;
  margin: 0 0 28px;
  max-width: 200px;
  margin-left: auto;
  margin-right: auto;
}
.floral-divider {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}
.div-line {
  height: 1px;
  width: 40px;
}
.div-line.left {
  background: linear-gradient(to right, transparent, #d4a8b0);
}
.div-line.right {
  background: linear-gradient(to left, transparent, #d4a8b0);
}

/* ─── Logo ─── */
.logo-header {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 18px;
}
.logo-wrap {
  width: 90px;
  height: 90px;
  border-radius: 50%;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #fff;
  box-shadow:
    0 4px 18px rgba(139, 107, 61, 0.15),
    0 0 0 3px rgba(212, 168, 176, 0.2);
  margin-bottom: 10px;
}
.logo-title {
  font-family: "Cormorant Garamond", Georgia, serif;
  font-size: 28px;
  font-weight: 700;
  color: #7a4e38;
  margin: 0 0 2px;
  letter-spacing: 0.04em;
}
.logo-sub {
  font-size: 9px;
  letter-spacing: 0.42em;
  text-transform: uppercase;
  color: #c4a882;
}

/* ─── Campos ─── */
.field-wrap {
  margin-bottom: 12px;
}
.field-label {
  display: block;
  font-size: 9px;
  font-weight: 600;
  letter-spacing: 0.28em;
  text-transform: uppercase;
  color: #c4a882;
  margin-bottom: 5px;
}
.field-inner {
  display: flex;
  align-items: center;
  border-radius: 12px;
  border: 1.5px solid rgba(212, 168, 176, 0.35);
  background: rgba(240, 232, 220, 0.4);
  transition: all 0.2s;
  padding: 10px 14px;
  gap: 10px;
}
.field-inner:focus-within {
  border-color: #d4a8b0;
  background: #fff;
  box-shadow: 0 0 0 3px rgba(212, 168, 176, 0.15);
}
.field-input {
  border: none;
  background: transparent;
  outline: none;
  font-size: 13px;
  color: #7a4e38;
  width: 100%;
  font-family: "Jost", sans-serif;
}
.eye-btn {
  border: none;
  background: none;
  cursor: pointer;
  padding: 0;
  color: #c4a882;
  display: flex;
  align-items: center;
}

/* ─── Misc ─── */
.forgot-row {
  text-align: right;
  margin-bottom: 16px;
}
.forgot-link {
  font-size: 11px;
  color: #a896a0;
  text-decoration: none;
  font-weight: 500;
}
.switch-row {
  text-align: center;
  font-size: 12px;
  color: #a896a0;
  margin-top: 14px;
}
.switch-link {
  color: #7a4e38;
  font-weight: 600;
  text-decoration: none;
  margin-left: 4px;
}
.error-msg {
  margin-bottom: 10px;
  padding: 8px 14px;
  border-radius: 10px;
  background: rgba(212, 120, 120, 0.1);
  border: 1px solid rgba(212, 120, 120, 0.25);
  font-size: 11px;
  color: #7a4e38;
  text-align: center;
}

/* ─── Botón ─── */
.submit-btn {
  width: 100%;
  padding: 13px;
  border: none;
  border-radius: 14px;
  cursor: pointer;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.22em;
  text-transform: uppercase;
  color: #fff;
  background: linear-gradient(135deg, #d4a8b0 0%, #c4a882 50%, #7a4e38 100%);
  box-shadow: 0 8px 24px rgba(122, 78, 56, 0.28);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-family: "Jost", sans-serif;
  transition:
    opacity 0.2s,
    transform 0.15s;
}
.submit-btn:hover:not(:disabled) {
  transform: translateY(-1px);
}

/* ─── Transición formulario (se espera a que el panel termine de deslizar) ─── */
.form-anim-enter-active {
  transition:
    opacity 0.32s ease 0.38s,
    transform 0.38s cubic-bezier(0.34, 1.4, 0.64, 1) 0.38s;
}
.form-anim-leave-active {
  transition:
    opacity 0.18s ease,
    transform 0.18s ease;
}
.form-anim-enter-from {
  opacity: 0;
  transform: translateY(10px) scale(0.97);
}
.form-anim-enter-to {
  opacity: 1;
  transform: none;
}
.form-anim-leave-from {
  opacity: 1;
  transform: none;
}
.form-anim-leave-to {
  opacity: 0;
  transform: translateY(-6px) scale(0.97);
}

/* ─── Transición texto del panel ─── */
.panel-text-enter-active {
  transition:
    opacity 0.3s ease 0.34s,
    transform 0.34s ease 0.34s;
}
.panel-text-leave-active {
  transition:
    opacity 0.16s ease,
    transform 0.16s ease;
}
.panel-text-enter-from {
  opacity: 0;
  transform: scale(0.92);
}
.panel-text-enter-to {
  opacity: 1;
  transform: scale(1);
}
.panel-text-leave-from {
  opacity: 1;
  transform: scale(1);
}
.panel-text-leave-to {
  opacity: 0;
  transform: scale(0.92);
}
</style>
