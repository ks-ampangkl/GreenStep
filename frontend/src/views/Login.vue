<template>
  <div class="auth-shell">
    <div class="auth-card">
      <div class="auth-brand">
        <span>🌿</span> GreenStep
      </div>
      <h1>Welcome back</h1>
      <p class="auth-sub">Log in to keep tracking your footprint.</p>

      <div v-if="error" class="error-banner">{{ error }}</div>

      <form @submit.prevent="submit">
        <div class="field">
          <label>Email</label>
          <input v-model="email" type="email" placeholder="you@example.com" required />
        </div>
        <div class="field">
          <label>Password</label>
          <input v-model="password" type="password" placeholder="••••••••" required />
        </div>
        <button class="btn-block">Log in</button>
      </form>

      <p class="auth-footer">
        New here? <router-link to="/register">Create an account</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import { login } from "../api/auth";
import { apiErrorMessage } from "../api/client";

const route = useRoute();
const router = useRouter();

const email = ref("");
const password = ref("");
const error = ref("");

async function submit() {
  error.value = "";
  try {
    const res = await login({ email: email.value, password: password.value });
    localStorage.setItem("token", res.data.access_token);
    router.push(route.query.redirect?.toString() || "/");
  } catch (err) {
    error.value = apiErrorMessage(err, "Something went wrong. Try again.");
  }
}
</script>
