<template>
  <header class="navbar">
    <div class="navbar-spacer"></div>
    <div class="navbar-actions">
      <router-link to="/log" class="btn btn-sm">
        <svg viewBox="0 0 20 20" fill="none" width="14" height="14"><path d="M10 4v12M4 10h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        Log Activity
      </router-link>
      <button class="btn-ghost btn-sm" @click="logout">
        Log out
      </button>
      <div class="navbar-avatar">{{ initials }}</div>
    </div>
  </header>
</template>

<script setup>
import { computed } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

const initials = computed(() => {
  const name = localStorage.getItem("user_name") || "Eco User";
  return name
    .split(" ")
    .map((p) => p[0])
    .join("")
    .slice(0, 2)
    .toUpperCase();
});

function logout() {
  localStorage.removeItem("token");
  router.push("/login");
}
</script>

<style scoped>
.navbar {
  height: 64px;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  padding: 0 32px;
  background: var(--color-surface);
  border-bottom: 1px solid var(--color-border);
}

.navbar-actions {
  display: flex;
  align-items: center;
  gap: 10px;
}

.navbar-avatar {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: var(--color-moss-soft);
  color: var(--color-forest);
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: var(--font-display);
  font-weight: 700;
  font-size: 13px;
  margin-left: 4px;
}

@media (max-width: 720px) {
  .navbar { padding: 0 16px; }
}
</style>
