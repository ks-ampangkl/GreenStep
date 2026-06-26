<template>
  <div class="auth-shell">
    <div class="auth-card">
      <div class="auth-brand">
        <span>🌿</span> GreenStep
      </div>
      <h1>Create your account</h1>
      <p class="auth-sub">Start logging activity and cutting your footprint.</p>

      <div v-if="error" class="error-banner">{{ error }}</div>
      <div v-if="success" class="success-banner">
        Account created — you can log in now.
      </div>

      <form @submit.prevent="submit">
        <div class="field">
          <label>Name</label>
          <input v-model="name" placeholder="Jordan Rivers" required />
        </div>
        <div class="field">
          <label>Email</label>
          <input v-model="email" type="email" placeholder="you@example.com" required />
        </div>
        <div class="field">
          <label>Password</label>
          <input v-model="password" type="password" placeholder="••••••••" required />
        </div>
        <button class="btn-block">Register</button>
      </form>

      <p class="auth-footer">
        Already have an account? <router-link to="/login">Log in</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { register } from "../api/auth";
import { apiErrorMessage } from "../api/client";

const name = ref("");
const email = ref("");
const password = ref("");
const error = ref("");
const success = ref(false);

async function submit() {
  error.value = "";
  success.value = false;
  try {
    await register({ name: name.value, email: email.value, password: password.value });
    success.value = true;
  } catch (err) {
    error.value = apiErrorMessage(err, "Couldn't create your account. Try again.");
  }
}
</script>
